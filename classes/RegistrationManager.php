<?php
require_once 'config/database.php';
require_once __DIR__ . '/ProgramManager.php';

class RegistrationManager {
    private $pdo;
    private $programManager;

    public function __construct(PDO $pdo, ProgramManager $programManager = null) {
        $this->pdo = $pdo;
        $this->programManager = $programManager ?: new ProgramManager($pdo);
    }

    private function calculatePrice($formData, $subjects) {
        try {
            error_log("calculatePrice Input: " . json_encode([
                'jenjang' => $formData['jenjang'],
                'package_type' => $formData['package_type'],
                'durasi' => $formData['durasi'],
                'jumlah_hari' => $formData['jumlah_hari'] ?? null,
                'subjects' => $subjects
            ]));

            // Fetch program data
            $programs = $this->programManager->getProgramsByCategory($formData['jenjang']);
            error_log("Programs Found: " . json_encode($programs));
            if (empty($programs)) {
                throw new Exception("No programs found for jenjang: {$formData['jenjang']}");
            }

            $program = $programs[0];
            $packages = $this->programManager->getProgramPackages($program['id']);
            error_log("Packages Found: " . json_encode($packages));

            // Map package_type dari form ke database
            $packageMap = [
                'kelas_reguler' => 'Kelas Reguler',
                'kelas_private_petung_girimukti' => 'Kelas Private - Petung/Girimukti',
                'kelas_private_luar_petung_girimukti' => 'Kelas Private - Luar Petung/Girimukti'
            ];
            $packageType = $packageMap[$formData['package_type']] ?? $formData['package_type'];

            // Find matching package
            $packageData = null;
            foreach ($packages as $package) {
                if ($package['package_type'] === $packageType) {
                    $packageData = $package;
                    break;
                }
            }

            if (!$packageData) {
                throw new Exception("Package not found: {$packageType}");
            }

            // Get base price per subject
            $durasiKey = match(strtolower($formData['durasi'])) {
                '8x pertemuan', '8x' => '8x',
                '12x pertemuan', '12x' => '12x',
                'harian' => 'harian',
                default => strtolower(str_replace(' ', '', $formData['durasi']))
            };

            $basePricePerSubject = null;
            foreach ($packageData['prices'] as $price) {
                $priceKey = match($price['price_label']) {
                    '8x Pertemuan' => '8x',
                    '12x Pertemuan' => '12x',
                    'Harian' => 'harian',
                    default => strtolower($price['price_label'])
                };
                if ($priceKey === $durasiKey) {
                    $basePricePerSubject = (float)$price['price'];
                    break;
                }
            }

            if ($basePricePerSubject === null) {
                throw new Exception("Price not found for durasi: {$formData['durasi']}");
            }

            $subjectCount = count($subjects);
            $jumlahHari = $formData['durasi'] === 'harian' ? (int)($formData['jumlah_hari'] ?? 1) : 1;

            // Calculate base price
            $totalBasePrice = $formData['durasi'] === 'harian'
                ? $basePricePerSubject * $subjectCount * $jumlahHari
                : $basePricePerSubject * $subjectCount;

            // Calculate transport cost for 'Kelas Private Luar Petung/Girimukti'
            $transportCost = 0;
            if ($formData['package_type'] === 'kelas_private_luar_petung_girimukti') {
                if ($formData['durasi'] === 'harian') {
                    $transportCost = 6250 * $jumlahHari * $subjectCount;
                } else {
                    $sessions = $durasiKey === '8x' ? 8 : 12;
                    $transportCost = 6250 * $sessions * $subjectCount;
                }
            }

            $totalPrice = $totalBasePrice + $transportCost;
            error_log("Calculated Price: " . $totalPrice);

            return $totalPrice;
        } catch (Exception $e) {
            error_log("Error calculating price: " . $e->getMessage());
            throw $e;
        }
    }

    public function saveRegistration($formData) {
        try {
            $this->pdo->beginTransaction();

            // Validate subjects
            $programs = $this->programManager->getProgramsByCategory($formData['jenjang']);
            $validSubjects = [];
            foreach ($programs as $program) {
                $subjects = is_array($program['subjects'])
                    ? $program['subjects']
                    : (is_string($program['subjects']) ? json_decode($program['subjects'], true) : []);
                $validSubjects = array_merge($validSubjects, $subjects);
            }
            $validSubjects = array_unique($validSubjects);

            $subjects = explode(',', $formData['mata_pelajaran']);
            $subjects = array_filter($subjects, function($subject) use ($validSubjects) {
                return in_array(trim($subject), $validSubjects);
            });

            if (empty($subjects)) {
                throw new Exception('No valid subjects selected');
            }

            // Cari package_id
            $packageMap = [
                'kelas_reguler' => 'Kelas Reguler',
                'kelas_private_petung_girimukti' => 'Kelas Private - Petung/Girimukti',
                'kelas_private_luar_petung_girimukti' => 'Kelas Private - Luar Petung/Girimukti'
            ];
            $packageTypeDB = $packageMap[$formData['package_type']] ?? $formData['package_type'];
            $stmt = $this->pdo->prepare("
                SELECT pp.id 
                FROM program_packages pp
                JOIN programs p ON pp.program_id = p.id
                WHERE pp.package_type = :package_type 
                AND p.category = :jenjang
            ");
            $stmt->execute([
                ':package_type' => $packageTypeDB,
                ':jenjang' => $formData['jenjang']
            ]);
            $package = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$package) {
                throw new Exception("Package ID tidak ditemukan untuk paket: {$packageTypeDB}");
            }
            $package_id = $package['id'];

            // Calculate total price
            $totalPrice = $this->calculatePrice($formData, $subjects);

            // Insert registration
            $query = "INSERT INTO pendaftaran (
                nama_lengkap, tanggal_lahir, jenis_kelamin, alamat, telepon, email, 
                jenjang, kelas, sekolah, package_id, package_type, durasi, jumlah_hari, 
                nama_ortu, pekerjaan_ortu, telepon_ortu, motivasi, referensi, total_price, status, created_at
            ) VALUES (
                :nama_lengkap, :tanggal_lahir, :jenis_kelamin, :alamat, :telepon, :email, 
                :jenjang, :kelas, :sekolah, :package_id, :package_type, :durasi, :jumlah_hari, 
                :nama_ortu, :pekerjaan_ortu, :telepon_ortu, :motivasi, :referensi, :total_price, 'pending', NOW()
            )";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':nama_lengkap' => $formData['nama_lengkap'],
                ':tanggal_lahir' => $formData['tanggal_lahir'],
                ':jenis_kelamin' => $formData['jenis_kelamin'],
                ':alamat' => $formData['alamat'],
                ':telepon' => $formData['telepon'],
                ':email' => $formData['email'] ?: null,
                ':jenjang' => $formData['jenjang'],
                ':kelas' => $formData['kelas'],
                ':sekolah' => $formData['sekolah'],
                ':package_id' => $package_id,
                ':package_type' => $packageTypeDB,
                ':durasi' => $formData['durasi'],
                ':jumlah_hari' => $formData['durasi'] === 'harian' ? (int)($formData['jumlah_hari'] ?? 1) : null,
                ':nama_ortu' => $formData['nama_ortu'],
                ':pekerjaan_ortu' => $formData['pekerjaan_ortu'] ?: null,
                ':telepon_ortu' => $formData['telepon_ortu'],
                ':motivasi' => $formData['motivasi'] ?: null,
                ':referensi' => $formData['referensi'] ?: null,
                ':total_price' => $totalPrice,
            ]);

            $registrationId = $this->pdo->lastInsertId();

            // Insert subjects
            $subjectQuery = "INSERT INTO registration_subjects (registration_id, subject_name) 
                            VALUES (:registration_id, :subject_name)";
            $subjectStmt = $this->pdo->prepare($subjectQuery);
            foreach ($subjects as $subject) {
                $subjectStmt->execute([
                    ':registration_id' => $registrationId,
                    ':subject_name' => trim($subject)
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error saving registration: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllRegistrations() {
        try {
            $query = "SELECT r.*, r.total_price FROM pendaftaran r ORDER BY r.created_at DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registrations as &$registration) {
                $subjectQuery = "SELECT subject_name FROM registration_subjects WHERE registration_id = :registration_id";
                $subjectStmt = $this->pdo->prepare($subjectQuery);
                $subjectStmt->execute([':registration_id' => $registration['id']]);
                $registration['subjects'] = array_column($subjectStmt->fetchAll(PDO::FETCH_ASSOC), 'subject_name');
            }

            return $registrations;
        } catch (Exception $e) {
            error_log("Error fetching registrations: " . $e->getMessage());
            return [];
        }
    }

    public function updateRegistrationStatus($id, $status) {
        try {
            $query = "UPDATE pendaftaran SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([
                ':id' => $id,
                ':status' => $status
            ]);
        } catch (Exception $e) {
            error_log("Error updating registration status: " . $e->getMessage());
            return false;
        }
    }

    public function deleteRegistration($id) {
        try {
            $this->pdo->beginTransaction();

            $this->pdo->exec("DELETE FROM registration_subjects WHERE registration_id = " . (int)$id);

            $query = "DELETE FROM pendaftaran WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error deleting registration: " . $e->getMessage());
            return false;
        }
    }
}
?>