<?php
require_once 'config/database.php';
require_once 'classes/ProgramManager.php';
require_once 'classes/RegistrationManager.php';

session_start();

$database = new Database();
$pdo = $database->getConnection();
$programManager = new ProgramManager($pdo);
$registrationManager = new RegistrationManager($pdo);

$message = '';
$messageType = '';

// Fetch program data directly from ProgramManager
$programData = [];
$categories = ['sd', 'smp', 'sma'];
foreach ($categories as $category) {
    $programs = $programManager->getProgramsByCategory($category);
    if (!empty($programs)) {
        $program = $programs[0]; // Assuming one program per category
        // Handle subjects field safely
        $subjects = is_array($program['subjects']) ? $program['subjects'] : (is_string($program['subjects']) ? json_decode($program['subjects'], true) : []);
        $programData[$category] = [
            'name' => $program['title'],
            'description' => $program['description'],
            'subjects' => $subjects ?? [],
            'packages' => []
        ];
        $packages = $programManager->getProgramPackages($program['id']);
        foreach ($packages as $package) {
            $packageKey = str_replace(' ', '_', strtolower($package['package_type']));
            $programData[$category]['packages'][$packageKey] = [
                'name' => $package['package_type'],
                'description' => $package['description'],
                'prices' => [],
                'additional' => $package['extra_info'] ?? null
            ];
            foreach ($package['prices'] as $price) {
                $priceKey = match($price['price_label']) {
                    '8x Pertemuan' => '8x',
                    '12x Pertemuan' => '12x',
                    'Harian' => 'harian',
                    default => strtolower($price['price_label'])
                };
                $programData[$category]['packages'][$packageKey]['prices'][$priceKey] = (float)$price['price'];
            }
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formData = [
        'nama_lengkap' => $_POST['namaLengkap'] ?? '',
        'tanggal_lahir' => $_POST['tanggalLahir'] ?? '',
        'jenis_kelamin' => $_POST['jenisKelamin'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'telepon' => $_POST['telepon'] ?? '',
        'email' => $_POST['email'] ?? '',
        'jenjang' => $_POST['jenjang'] ?? '',
        'kelas' => $_POST['kelas'] ?? '',
        'sekolah' => $_POST['sekolah'] ?? '',
        'package_type' => $_POST['package_type'] ?? '',
        'durasi' => $_POST['durasi'] ?? '',
        'jumlah_hari' => $_POST['jumlahHari'] ?? null,
        'nama_ortu' => $_POST['namaOrtu'] ?? '',
        'pekerjaan_ortu' => $_POST['pekerjaanOrtu'] ?? '',
        'telepon_ortu' => $_POST['teleponOrtu'] ?? '',
        'motivasi' => $_POST['motivasi'] ?? '',
        'referensi' => $_POST['referensi'] ?? '',
        'mata_pelajaran' => $_POST['mata_pelajaran'] ?? ''
    ];

    // Normalize durasi
    $durasiMap = [
        'harian' => 'harian',
        '8x' => '8x',
        '12x' => '12x',
        'Harian' => 'harian',
        '8x Pertemuan' => '8x',
        '12x Pertemuan' => '12x'
    ];
    $formData['durasi'] = $durasiMap[$formData['durasi']] ?? $formData['durasi'];
    $persetujuan = isset($_POST['persetujuan']) ? 1 : 0;

    $requiredFields = [
        'nama_lengkap', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'telepon',
        'jenjang', 'kelas', 'sekolah', 'package_type', 'durasi', 'nama_ortu',
        'telepon_ortu', 'mata_pelajaran'
    ];

    $mataPelajaran = explode(',', $formData['mata_pelajaran']);
    if (empty($mataPelajaran) || !is_array($mataPelajaran)) {
        $message = 'Minimal satu mata pelajaran harus dipilih!';
        $messageType = 'error';
        $isValid = false;
    } else {
        $isValid = true;
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                $isValid = false;
                $message = 'Mohon lengkapi semua field yang wajib diisi!';
                $messageType = 'error';
                break;
            }
        }

        if ($formData['durasi'] === 'harian' && (empty($formData['jumlah_hari']) || (int)$formData['jumlah_hari'] < 1 || (int)$formData['jumlah_hari'] > 30)) {
            $isValid = false;
            $message = 'Jumlah hari harus antara 1-30 untuk durasi harian!';
            $messageType = 'error';
        }

        if (!$persetujuan) {
            $isValid = false;
            $message = 'Mohon menyetujui syarat dan ketentuan!';
            $messageType = 'error';
        }

        if ($isValid) {
            try {
                // Validate mata pelajaran against program subjects
                $programs = $programManager->getProgramsByCategory($formData['jenjang']);
                if (empty($programs)) {
                    throw new Exception('Program tidak ditemukan untuk jenjang ini');
                }
                $validSubjects = is_array($programs[0]['subjects']) ? $programs[0]['subjects'] : json_decode($programs[0]['subjects'], true);
                foreach ($mataPelajaran as $subject) {
                    if (!in_array(trim($subject), $validSubjects)) {
                        throw new Exception("Mata pelajaran tidak valid: {$subject}");
                    }
                }

                // Save registration
                if ($registrationManager->saveRegistration($formData)) {
                    $message = 'Pendaftaran berhasil! Tim kami akan menghubungi Anda dalam 1x24 jam.';
                    $messageType = 'success';
                    $_POST = [];
                } else {
                    $message = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                    $messageType = 'error';
                }
            } catch (Exception $e) {
                $message = 'Terjadi kesalahan: ' . htmlspecialchars($e->getMessage());
                $messageType = 'error';
                error_log('Registration Error: ' . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pendaftaran - Bimbingan Belajar Peta Ilmu</title>
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="styledaftar.css" />
    <script src="universal.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-wrapper">
                <div class="logo">
                    <a href="index.php">
                        <img src="images/IMG_3898.PNG" alt="Logo Peta Ilmu" />
                        <span>Peta Ilmu</span>
                    </a>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="program.php">Program</a></li>
                        <li><a href="pendaftaran.php" class="active">Pendaftaran</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                        <li><a href="siswa_login.php" class="btn-login-siswa"><i class="fas fa-sign-in-alt"></i> Login Siswa</a></li>
                    </ul>
                </nav>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="registration-hero">
        <div class="container">
            <h1>Daftar Sekarang</h1>
            <p>Bergabunglah dengan siswa yang telah merasakan bimbingan belajar di Peta Ilmu. Wujudkan prestasi terbaikmu bersama kami!</p>
        </div>
    </section>

    <!-- Registration Section -->
    <section class="registration-section">
        <div class="registration-container">
            <!-- Registration Information -->
            <div class="registration-info">
                <h2>Informasi Pendaftaran</h2>
                <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="info-content">
                        <h3>Periode Pendaftaran</h3>
                        <p>Pendaftaran dibuka sepanjang tahun. Kelas dimulai setiap awal bulan.</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-file-alt"></i>
                    <div class="info-content">
                        <h3>Dokumen yang Diperlukan</h3>
                        <p>Fotokopi kartu identitas, pas foto 3x4 (2 lembar), dan fotokopi raport terakhir</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div class="info-content">
                        <h3>Jadwal Belajar</h3>
                        <p>Senin-Sabtu: 17:00-21:00, Minggu: Libur</p>
                    </div>
                </div>
                <div class="program-options">
                    <h3>Program Yang Tersedia</h3>
                    <div class="program-grid">
                        <div class="program-option">
                            <h4>Program SD</h4>
                            <p>Kelas 1-6</p>
                        </div>
                        <div class="program-option">
                            <h4>Program SMP</h4>
                            <p>Kelas 7-9</p>
                        </div>
                        <div class="program-option">
                            <h4>Program SMA</h4>
                            <p>Kelas 10-12</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="registration-form">
                <div class="form-title">
                    <h2>Formulir Pendaftaran</h2>
                    <p>Lengkapi data berikut untuk mendaftar di Bimbingan Belajar Peta Ilmu</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo htmlspecialchars($messageType); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form id="registrationForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <!-- Data Siswa -->
                    <h3 style="color: #667eea; margin-bottom: 20px; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                        <i class="fas fa-user"></i> Data Siswa
                    </h3>
                    <div class="form-group">
                        <label for="namaLengkap">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="namaLengkap" name="namaLengkap" class="form-control" value="<?php echo htmlspecialchars($_POST['namaLengkap'] ?? ''); ?>" required />
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggalLahir">Tanggal Lahir <span class="required">*</span></label>
                            <input type="date" id="tanggalLahir" name="tanggalLahir" class="form-control" value="<?php echo htmlspecialchars($_POST['tanggalLahir'] ?? ''); ?>" required />
                        </div>
                        <div class="form-group">
                            <label for="jenisKelamin">Jenis Kelamin <span class="required">*</span></label>
                            <select id="jenisKelamin" name="jenisKelamin" class="form-control" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="laki-laki" <?php echo (isset($_POST['jenisKelamin']) && $_POST['jenisKelamin'] == 'laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="perempuan" <?php echo (isset($_POST['jenisKelamin']) && $_POST['jenisKelamin'] == 'perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                        <textarea id="alamat" name="alamat" class="form-control" placeholder="Masukkan alamat lengkap" required><?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telepon">Nomor Telepon/HP <span class="required">*</span></label>
                            <input type="tel" id="telepon" name="telepon" class="form-control" placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($_POST['telepon'] ?? ''); ?>" required />
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="example@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                        </div>
                    </div>

                    <!-- Data Akademik -->
                    <h3 style="color: #667eea; margin: 30px 0 20px 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                        <i class="fas fa-graduation-cap"></i> Data Akademik
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="jenjang">Jenjang Pendidikan <span class="required">*</span></label>
                            <select id="jenjang" name="jenjang" class="form-control" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="sd" <?php echo (isset($_POST['jenjang']) && $_POST['jenjang'] == 'sd') ? 'selected' : ''; ?>>SD/MI</option>
                                <option value="smp" <?php echo (isset($_POST['jenjang']) && $_POST['jenjang'] == 'smp') ? 'selected' : ''; ?>>SMP/MTs</option>
                                <option value="sma" <?php echo (isset($_POST['jenjang']) && $_POST['jenjang'] == 'sma') ? 'selected' : ''; ?>>SMA/MA/SMK</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kelas">Kelas <span class="required">*</span></label>
                            <select id="kelas" name="kelas" class="form-control" required>
                                <option value="">Pilih Kelas</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sekolah">Asal Sekolah <span class="required">*</span></label>
                        <input type="text" id="sekolah" name="sekolah" class="form-control" placeholder="Nama sekolah lengkap" value="<?php echo htmlspecialchars($_POST['sekolah'] ?? ''); ?>" required />
                    </div>

                    <!-- Program Selection -->
                    <div class="program-selection" id="programSelection">
                        <h3 style="color: #667eea; margin-bottom: 15px">
                            <i class="fas fa-book"></i> Pilihan Program & Paket
                        </h3>
                        <div class="form-group">
                            <label for="package_type">Pilih Paket Program <span class="required">*</span></label>
                            <select id="package_type" name="package_type" class="form-control" required>
                                <option value="">Pilih jenjang pendidikan terlebih dahulu</option>
                            </select>
                            <input type="hidden" name="mata_pelajaran" id="mataPelajaranHidden" value="<?php echo htmlspecialchars($_POST['mata_pelajaran'] ?? ''); ?>">
                            <input type="hidden" name="total_price" id="totalPriceHidden" value="<?php echo htmlspecialchars($_POST['total_price'] ?? ''); ?>">
                        </div>
                        <div class="form-group" id="durasiGroup" style="display: none">
                            <label for="durasi">Pilih Durasi <span class="required">*</span></label>
                            <select id="durasi" name="durasi" class="form-control" required>
                                <option value="">Pilih Durasi</option>
                            </select>
                        </div>
                        <div class="program-details" id="programDetails">
                            <div id="programInfo"></div>
                        </div>
                        <div class="price-display" id="priceDisplay">
                            <div id="priceInfo"></div>
                        </div>
                    </div>

                    <!-- Data Orang Tua -->
                    <h3 style="color: #667eea; margin: 30px 0 20px 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                        <i class="fas fa-users"></i> Data Orang Tua/Wali
                    </h3>
                    <div class="form-group">
                        <label for="namaOrtu">Nama Orang Tua/Wali <span class="required">*</span></label>
                        <input type="text" id="namaOrtu" name="namaOrtu" class="form-control" value="<?php echo htmlspecialchars($_POST['namaOrtu'] ?? ''); ?>" required />
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pekerjaanOrtu">Pekerjaan</label>
                            <input type="text" id="pekerjaanOrtu" name="pekerjaanOrtu" class="form-control" value="<?php echo htmlspecialchars($_POST['pekerjaanOrtu'] ?? ''); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="teleponOrtu">Nomor Telepon <span class="required">*</span></label>
                            <input type="tel" id="teleponOrtu" name="teleponOrtu" class="form-control" value="<?php echo htmlspecialchars($_POST['teleponOrtu'] ?? ''); ?>" required />
                        </div>
                    </div>

                    <!-- Informasi Tambahan -->
                    <h3 style="color: #667eea; margin: 30px 0 20px 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                        <i class="fas fa-info-circle"></i> Informasi Tambahan
                    </h3>
                    <div class="form-group">
                        <label for="motivasi">Motivasi/Tujuan Mengikuti Bimbel</label>
                        <textarea id="motivasi" name="motivasi" class="form-control" placeholder="Ceritakan motivasi Anda mengikuti bimbingan belajar di Peta Ilmu"><?php echo htmlspecialchars($_POST['motivasi'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="referensi">Mengetahui Peta Ilmu dari mana?</label>
                        <select id="referensi" name="referensi" class="form-control">
                            <option value="">Pilih Sumber Info</option>
                            <option value="teman" <?php echo (isset($_POST['referensi']) && $_POST['referensi'] == 'teman') ? 'selected' : ''; ?>>Teman/Saudara</option>
                            <option value="internet" <?php echo (isset($_POST['referensi']) && $_POST['referensi'] == 'internet') ? 'selected' : ''; ?>>Internet/Website</option>
                            <option value="sosmed" <?php echo (isset($_POST['referensi']) && $_POST['referensi'] == 'sosmed') ? 'selected' : ''; ?>>Media Sosial</option>
                            <option value="brosur" <?php echo (isset($_POST['referensi']) && $_POST['referensi'] == 'brosur') ? 'selected' : ''; ?>>Brosur/Pamflet</option>
                            <option value="lainnya" <?php echo (isset($_POST['referensi']) && $_POST['referensi'] == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>

                    <!-- Persetujuan -->
                    <div class="checkbox-group">
                        <input type="checkbox" id="persetujuan" name="persetujuan" <?php echo (isset($_POST['persetujuan'])) ? 'checked' : ''; ?> required />
                        <label for="persetujuan">
                            Saya menyetujui <a href="#" style="color: #667eea">syarat dan ketentuan</a> yang berlaku di Bimbingan Belajar Peta Ilmu dan bersedia mengikuti aturan yang telah ditetapkan. <span class="required">*</span>
                        </label>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Kirim Pendaftaran
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Process Steps Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-header">
                <h2>Cara Mendaftar</h2>
                <p>Ikuti langkah-langkah mudah berikut untuk menjadi bagian dari keluarga besar Peta Ilmu</p>
            </div>
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h3>Isi Formulir</h3>
                    <p>Lengkapi formulir pendaftaran online dengan data yang akurat dan benar</p>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h3>Konfirmasi</h3>
                    <p>Tim kami akan menghubungi Anda dalam 1x24 jam untuk konfirmasi data</p>
                </div>
                <!--<div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Pembayaran</h3>
                    <p>Lakukan pembayaran administrasi dan biaya bimbingan sesuai program pilihan</p>
                </div>-->
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h3>Mulai Belajar</h3>
                    <p>Selamat! Anda sudah terdaftar dan siap memulai perjalanan belajar di Peta Ilmu</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2>Pertanyaan yang Sering Diajukan</h2>
                <p>Temukan jawaban atas pertanyaan umum seputar pendaftaran di Peta Ilmu</p>
            </div>
            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apakah ada tes masuk untuk mendaftar di Peta Ilmu?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Tidak ada tes masuk khusus. Kami melakukan placement test sederhana untuk menentukan kelas yang sesuai dengan kemampuan siswa agar pembelajaran lebih efektif.
                        </div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Berapa lama masa belajar di Peta Ilmu?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Masa belajar fleksibel tergantung program yang dipilih.
                        </div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apakah bisa mendaftar di tengah semester?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Ya, pendaftaran terbuka sepanjang tahun. Untuk siswa yang masuk di tengah semester akan mendapat materi penyesuaian agar dapat mengikuti pembelajaran dengan baik.
                        </div>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Bagaimana jika ingin pindah kelas atau program?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            Perpindahan kelas atau program dapat dilakukan dengan berkonsultasi terlebih dahulu dengan koordinator akademik untuk menentukan kelas yang paling sesuai.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="images/IMG_3899.PNG" alt="Logo Peta Ilmu" />
                    <h3>Bimbingan Belajar Peta Ilmu</h3>
                    <p>Di setiap tempat, di situ ilmu didapat!</p>
                </div>
                <div class="footer-links">
                    <h4>Link Cepat</h4>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="profil.php">Profil</a></li>
                        <li><a href="program.php">Program</a></li>
                        <li><a href="pendaftaran.php">Pendaftaran</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-program">
                    <h4>Program Kami</h4>
                    <ul>
                        <li><a href="program.php#sd">Program SD</a></li>
                        <li><a href="program.php#smp">Program SMP</a></li>
                        <li><a href="program.php#sma">Program SMA</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Kontak Kami</h4>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Perumahan Nuansa Petung Blok C No.9</li>
                        <li><i class="fas fa-map-marker-alt"></i> Girimukti Rt.10 No.55 Strat 3</li>
                        <li><i class="fas fa-phone"></i> +62 8981792917</li>
                        <li><i class="fas fa-phone"></i> +62 82255131993</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Bimbingan Belajar Peta Ilmu. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        <?php if (empty($programData)) {
    $programData = [
        'sd' => [
            'name' => 'Program SD (Sekolah Dasar)',
            'description' => 'Program pembelajaran lengkap untuk siswa SD kelas 1-6 meliputi semua mata pelajaran utama (kecuali agama dan olahraga) dengan metode yang menyenangkan dan interaktif.',
            'subjects' => ['Matematika', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris', 'Seni Budaya', 'PKn'],
            'packages' => [
                'kelas_reguler' => [
                    'name' => 'Kelas Reguler',
                    'description' => 'Max 5 Siswa : 1 Guru (Di tempat bimbel)',
                    'prices' => [
                        '8x' => 160000,
                        '12x' => 240000,
                        'harian' => 30000
                    ],
                    'additional' => null
                ],
                'kelas_private_petung_girimukti' => [
                    'name' => 'Kelas Private - Petung/Girimukti',
                    'description' => '1 Siswa : 1 Guru (Guru datang ke rumah)',
                    'prices' => [
                        '8x' => 200000,
                        '12x' => 300000,
                        'harian' => 35000
                    ],
                    'additional' => null
                ],
                'kelas_private_luar_petung_girimukti' => [
                    'name' => 'Kelas Private - Luar Petung/Girimukti',
                    'description' => '1 Siswa : 1 Guru (Guru datang ke rumah)',
                    'prices' => [
                        '8x' => 240000,
                        '12x' => 360000,
                        'harian' => 40000
                    ],
                    'additional' => '*Tambahan biaya transportasi guru: Rp 6.250/pertemuan'
                ]
            ]
        ],
        'smp' => [
            'name' => 'Program SMP (Sekolah Menengah Pertama)',
            'description' => 'Program yang dirancang khusus untuk siswa SMP dengan fokus pada tiga mata pelajaran inti: Matematika, IPA, dan Bahasa Inggris untuk membangun fondasi akademik yang kuat.',
            'subjects' => ['Matematika', 'IPA (Fisika & Biologi)', 'Bahasa Inggris'],
            'packages' => [
                'kelas_reguler' => [
                    'name' => 'Kelas Reguler',
                    'description' => 'Max 5 Siswa : 1 Guru (Di tempat bimbel)',
                    'prices' => [
                        '8x' => 200000,
                        '12x' => 300000,
                        'harian' => 35000
                    ],
                    'additional' => null
                ],
                'kelas_private_petung_girimukti' => [
                    'name' => 'Kelas Private - Petung/Girimukti',
                    'description' => '1 Siswa : 1 Guru (Guru datang ke rumah)',
                    'prices' => [
                        '8x' => 240000,
                        '12x' => 360000,
                        'harian' => 40000
                    ],
                    'additional' => null
                ],
                'kelas_private_luar_petung_girimukti' => [
                    'name' => 'Kelas Private - Luar Petung/Girimukti',
                    'description' => '1 Siswa : 1 Guru (Guru datang ke rumah)',
                    'prices' => [
                        '8x' => 280000,
                        '12x' => 420000,
                        'harian' => 45000
                    ],
                    'additional' => '*Tambahan biaya transportasi guru: Rp 6.250/pertemuan'
                ]
            ]
        ],
        'sma' => [
            'name' => 'Program SMA (Sekolah Menengah Atas)',
            'description' => 'Program intensif untuk siswa SMA dengan fokus pada mata pelajaran sains. Persiapan optimal untuk masuk perguruan tinggi jurusan sains dan teknik.',
            'subjects' => ['Matematika', 'Fisika', 'Kimia', 'Biologi'],
            'packages' => [
                'kelas_reguler' => [
                    'name' => 'Kelas Reguler',
                    'description' => 'Max 5 Siswa : 1 Guru (Di tempat bimbel)',
                    'prices' => [
                        '8x' => 240000,
                        '12x' => 360000,
                        'harian' => 40000
                    ],
                    'additional' => null
                ],
                'kelas_private_petung_girimukti' => [
                    'name' => 'Kelas Private - Petung/Girimukti',
                    'description' => '1 Siswa : 1 Guru (Guru datang ke rumah)',
                    'prices' => [
                        '8x' => 320000,
                        '12x' => 480000,
                        'harian' => 45000
                    ],
                    'additional' => null
                ],
                'kelas_private_luar_petung_girimukti' => [
                    'name' => 'Kelas Private - Luar Petung/Girimukti',
                    'description' => '1 Siswa : 1 Guru (Guru datang ke rumah)',
                    'prices' => [
                        '8x' => 360000,
                        '12x' => 540000,
                        'harian' => 50000
                    ],
                    'additional' => '*Tambahan biaya transportasi guru: Rp 6.250/pertemuan'
                ]
            ]
        
        ]
    ];
}
?>
        // Inject program data directly into JavaScript
        const programData = <?php echo json_encode($programData); ?>;
        
        // Handle pre-selected subjects after form submission
        <?php if (isset($_POST['mata_pelajaran']) && !empty($_POST['mata_pelajaran'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const selectedSubjects = <?php echo json_encode(explode(',', $_POST['mata_pelajaran'])); ?>;
                setTimeout(function() {
                    selectedSubjects.forEach(subject => {
                        const checkbox = document.querySelector(`input[name="mataPelajaran[]"][value="${subject}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                }, 300);
            });
        <?php endif; ?>

        // Restore form state after submission
        <?php if (isset($_POST['jenjang']) && !empty($_POST['jenjang'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('jenjang').value = '<?php echo htmlspecialchars($_POST['jenjang']); ?>';
                var event = new Event('change');
                document.getElementById('jenjang').dispatchEvent(event);
                <?php if (isset($_POST['kelas']) && !empty($_POST['kelas'])): ?>
                    setTimeout(function() {
                        document.getElementById('kelas').value = '<?php echo htmlspecialchars($_POST['kelas']); ?>';
                    }, 100);
                <?php endif; ?>
                <?php if (isset($_POST['package_type']) && !empty($_POST['package_type'])): ?>
                    setTimeout(function() {
                        document.getElementById('package_type').value = '<?php echo htmlspecialchars($_POST['package_type']); ?>';
                        var event2 = new Event('change');
                        document.getElementById('package_type').dispatchEvent(event2);
                        <?php if (isset($_POST['durasi']) && !empty($_POST['durasi'])): ?>
                            setTimeout(function() {
                                document.getElementById('durasi').value = '<?php echo htmlspecialchars($_POST['durasi']); ?>';
                            }, 200);
                        <?php endif; ?>
                    }, 150);
                <?php endif; ?>
            });
        <?php endif; ?>
    </script>
</body>
</html>