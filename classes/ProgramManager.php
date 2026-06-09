<?php
require_once __DIR__ . '/../config/database.php';

class ProgramManager {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAllPrograms() {
        try {
            $query = "SELECT * FROM programs ORDER BY id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($programs as &$program) {
                $program['subjects'] = json_decode($program['subjects'] ?? '[]', true) ?? [];
                $program['features'] = $this->getProgramFeatures($program['id']);
                $program['packages'] = $this->getProgramPackages($program['id']);
            }

            return $programs;
        } catch (Exception $e) {
            error_log("Error fetching programs: " . $e->getMessage());
            return [];
        }
    }

    public function getProgramById($id) {
        try {
            $query = "SELECT * FROM programs WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            $program = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($program) {
                $program['subjects'] = json_decode($program['subjects'] ?? '[]', true) ?? [];
                $program['features'] = $this->getProgramFeatures($id);
                $program['packages'] = $this->getProgramPackages($id);
            }

            return $program ?: [];
        } catch (Exception $e) {
            error_log("Error fetching program by ID: " . $e->getMessage());
            return [];
        }
    }

    public function getProgramsByCategory($category) {
        try {
            $query = "SELECT * FROM programs WHERE category = :category";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':category' => $category]);
            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($programs as &$program) {
                $program['subjects'] = json_decode($program['subjects'] ?? '[]', true) ?? [];
                $program['features'] = $this->getProgramFeatures($program['id']);
                $program['packages'] = $this->getProgramPackages($program['id']);
            }

            return $programs;
        } catch (Exception $e) {
            error_log("Error fetching programs by category: " . $e->getMessage());
            return [];
        }
    }

    public function addProgram($data) {
        try {
            $this->pdo->beginTransaction();

            $query = "INSERT INTO programs (program_code, category, icon, title, description, duration, frequency, subjects)
                      VALUES (:program_code, :category, :icon, :title, :description, :duration, :frequency, :subjects)";
            $stmt = $this->pdo->prepare($query);
            $subjectsJson = json_encode($data['subjects'] ?? []);
            $stmt->execute([
                ':program_code' => $data['program_code'],
                ':category' => $data['category'],
                ':icon' => $data['icon'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':duration' => $data['duration'],
                ':frequency' => $data['frequency'],
                ':subjects' => $subjectsJson
            ]);

            $programId = $this->pdo->lastInsertId();

            if (!empty($data['features'])) {
                $featureQuery = "INSERT INTO program_features (program_id, feature_text) VALUES (:program_id, :feature_text)";
                $featureStmt = $this->pdo->prepare($featureQuery);
                foreach ($data['features'] as $feature) {
                    $featureStmt->execute([
                        ':program_id' => $programId,
                        ':feature_text' => trim($feature)
                    ]);
                }
            }

            if (!empty($data['packages'])) {
                $packageQuery = "INSERT INTO program_packages (program_id, package_type, description, package_icon, info, extra_info)
                                 VALUES (:program_id, :package_type, :description, :package_icon, :info, :extra_info)";
                $packageStmt = $this->pdo->prepare($packageQuery);

                $priceQuery = "INSERT INTO package_prices (package_id, price_label, price)
                               VALUES (:package_id, :price_label, :price)";
                $priceStmt = $this->pdo->prepare($priceQuery);

                foreach ($data['packages'] as $package) {
                    $packageStmt->execute([
                        ':program_id' => $programId,
                        ':package_type' => $package['package_type'],
                        ':description' => $package['description'],
                        ':package_icon' => $package['package_icon'],
                        ':info' => $package['info'] ?? '',
                        ':extra_info' => $package['extra_info'] ?? ''
                    ]);

                    $packageId = $this->pdo->lastInsertId();

                    foreach ($package['prices'] as $price) {
                        $priceStmt->execute([
                            ':package_id' => $packageId,
                            ':price_label' => $price['price_label'],
                            ':price' => $price['price']
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Error adding program: " . $e->getMessage());
            return false;
        }
    }

    public function validateProgramPackage($packageId) {
    try {
        $query = "SELECT id FROM program_packages WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $packageId]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        error_log("Error validating package: " . $e->getMessage());
        return false;
    }
}

    public function updateProgram($data) {
        try {
            $this->pdo->beginTransaction();

            $query = "UPDATE programs 
                      SET program_code = :program_code, category = :category, icon = :icon, 
                          title = :title, description = :description, duration = :duration, 
                          frequency = :frequency, subjects = :subjects
                      WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $subjectsJson = json_encode($data['subjects'] ?? []);
            $stmt->execute([
                ':id' => $data['id'],
                ':program_code' => $data['program_code'],
                ':category' => $data['category'],
                ':icon' => $data['icon'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':duration' => $data['duration'],
                ':frequency' => $data['frequency'],
                ':subjects' => $subjectsJson
            ]);

            $this->pdo->exec("DELETE FROM program_features WHERE program_id = " . (int)$data['id']);
            if (!empty($data['features'])) {
                $featureQuery = "INSERT INTO program_features (program_id, feature_text) VALUES (:program_id, :feature_text)";
                $featureStmt = $this->pdo->prepare($featureQuery);
                foreach ($data['features'] as $feature) {
                    $featureStmt->execute([
                        ':program_id' => $data['id'],
                        ':feature_text' => trim($feature)
                    ]);
                }
            }

            $this->pdo->exec("DELETE FROM package_prices WHERE package_id IN (SELECT id FROM program_packages WHERE program_id = " . (int)$data['id'] . ")");
            $this->pdo->exec("DELETE FROM program_packages WHERE program_id = " . (int)$data['id']);

            if (!empty($data['packages'])) {
                $packageQuery = "INSERT INTO program_packages (program_id, package_type, description, package_icon, info, extra_info)
                                 VALUES (:program_id, :package_type, :description, :package_icon, :info, :extra_info)";
                $packageStmt = $this->pdo->prepare($packageQuery);

                $priceQuery = "INSERT INTO package_prices (package_id, price_label, price)
                               VALUES (:package_id, :price_label, :price)";
                $priceStmt = $this->pdo->prepare($priceQuery);

                foreach ($data['packages'] as $package) {
                    $packageStmt->execute([
                        ':program_id' => $data['id'],
                        ':package_type' => $package['package_type'],
                        ':description' => $package['description'],
                        ':package_icon' => $package['package_icon'],
                        ':info' => $package['info'] ?? '',
                        ':extra_info' => $package['extra_info'] ?? ''
                    ]);

                    $packageId = $this->pdo->lastInsertId();

                    foreach ($package['prices'] as $price) {
                        $priceStmt->execute([
                            ':package_id' => $packageId,
                            ':price_label' => $price['price_label'],
                            ':price' => $price['price']
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Error updating program: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProgram($id) {
        try {
            $this->pdo->beginTransaction();

            $this->pdo->exec("DELETE FROM package_prices WHERE package_id IN (SELECT id FROM program_packages WHERE program_id = " . (int)$id . ")");
            $this->pdo->exec("DELETE FROM program_packages WHERE program_id = " . (int)$id);
            $this->pdo->exec("DELETE FROM program_features WHERE program_id = " . (int)$id);

            $query = "DELETE FROM programs WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollback();
            error_log("Error deleting program: " . $e->getMessage());
            return false;
        }
    }

    public function getProgramFeatures($program_id) {
        try {
            $query = "SELECT feature_text FROM program_features WHERE program_id = :program_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':program_id' => $program_id]);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'feature_text');
        } catch (Exception $e) {
            error_log("Error fetching program features: " . $e->getMessage());
            return [];
        }
    }

            public function getProgramPackages($programId) {
                $query = "SELECT package_type, description, extra_info FROM program_packages WHERE program_id = :program_id";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([':program_id' => $programId]);
                $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($packages as &$package) {
                    $priceQuery = "SELECT price_label, price FROM package_prices WHERE package_id = (
                        SELECT id FROM program_packages WHERE program_id = :program_id AND package_type = :package_type
                    )";
                    $priceStmt = $this->pdo->prepare($priceQuery);
                    $priceStmt->execute([
                        ':program_id' => $programId,
                        ':package_type' => $package['package_type']
                    ]);
                    $package['prices'] = $priceStmt->fetchAll(PDO::FETCH_ASSOC);
                }

                return $packages;
            }

            public function getProgramBenefits() {
            $query = "SELECT icon, title, description FROM program_benefits ORDER BY id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function getProgramFAQs() {
            $query = "SELECT question, answer FROM program_faqs ORDER BY id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    public function getFallbackPrograms() {
        return [
            [
                'id' => 1,
                'category' => 'sd',
                'title' => 'Program SD',
                'description' => 'Program pembelajaran lengkap untuk siswa SD kelas 1-6.',
                'duration' => '1-2 jam per sesi',
                'frequency' => '2-3 kali per minggu',
                'subjects' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA'],
                'features' => ['Matematika - konsep dasar hingga operasi hitung lanjutan', 'Bahasa Indonesia - membaca, menulis, berbicara'],
                'packages' => [
                    ['package_type' => 'Kelas Reguler', 'description' => 'Max 5 Siswa : 1 Guru', 'package_icon' => 'fas fa-users', 'prices' => [['price_label' => '8x', 'price' => 160000], ['price_label' => '12x', 'price' => 240000]]],
                    ['package_type' => 'Kelas Private', 'description' => '1 Siswa : 1 Guru', 'package_icon' => 'fas fa-home', 'prices' => [['price_label' => '8x', 'price' => 200000], ['price_label' => '12x', 'price' => 300000]]]
                ]
            ]
        ];
    }

    public function getFallbackBenefits() {
        return $this->getProgramBenefits();
    }

    public function getFallbackFAQs() {
        return $this->getProgramFAQs();
    }
}
?>