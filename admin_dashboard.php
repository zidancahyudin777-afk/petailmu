<?php
session_start();
require_once 'config/database.php';
require_once 'classes/ProgramManager.php';
require_once 'classes/RegistrationManager.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$database = new Database();
$pdo = $database->getConnection();
$programManager = new ProgramManager($pdo);
$registrationManager = new RegistrationManager($pdo);

$section = $_GET['section'] ?? 'dashboard';
$action = $_GET['action'] ?? '';
$message = '';
$messageType = '';

if ($section == 'registration' && $action == 'update_status' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    try {
        if ($registrationManager->updateRegistrationStatus($id, $status)) {
            $message = "Status pendaftaran berhasil diperbarui!";
            $messageType = "success";
        } else {
            $message = "Gagal memperbarui status pendaftaran.";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
        $messageType = "error";
    }
}

if ($section == 'registration' && $action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? 0;
    try {
        if ($registrationManager->deleteRegistration($id)) {
            $message = "Pendaftaran berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Gagal menghapus pendaftaran.";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
        $messageType = "error";
    }
}

if ($section == 'program' && $action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'program_code' => 'PROG' . time(),
        'category' => $_POST['category'],
        'icon' => $_POST['icon'] ?? 'fas fa-book',
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'duration' => $_POST['duration'],
        'frequency' => $_POST['frequency'],
        'subjects' => array_map('trim', explode(',', $_POST['subjects'] ?? '')),
        'features' => array_map('trim', explode(',', $_POST['features'] ?? '')),
        'packages' => []
    ];

    if (!empty($_POST['packages'])) {
        $packages = explode(',', $_POST['packages']);
        foreach ($packages as $pkg) {
            $pkgData = explode('|', $pkg);
            if (count($pkgData) >= 4) {
                $prices = explode(';', $pkgData[3]);
                $priceArray = [];
                foreach ($prices as $price) {
                    $priceParts = explode(':', $price);
                    if (count($priceParts) == 2) {
                        $priceArray[] = ['price_label' => $priceParts[0], 'price' => $priceParts[1]];
                    }
                }
                $data['packages'][] = [
                    'package_type' => $pkgData[0],
                    'description' => $pkgData[1],
                    'package_icon' => $pkgData[2],
                    'prices' => $priceArray,
                    'info' => $pkgData[4] ?? '',
                    'extra_info' => $pkgData[5] ?? ''
                ];
            }
        }
    }

    try {
        if ($programManager->addProgram($data)) {
            $message = "Program berhasil ditambahkan!";
            $messageType = "success";
        } else {
            $message = "Gagal menambahkan program.";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
        $messageType = "error";
    }
}

if ($section == 'program' && $action == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'id' => $_POST['id'],
        'program_code' => $_POST['program_code'] ?? 'PROG' . time(),
        'category' => $_POST['category'],
        'icon' => $_POST['icon'] ?? 'fas fa-book',
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'duration' => $_POST['duration'],
        'frequency' => $_POST['frequency'],
        'subjects' => array_map('trim', explode(',', $_POST['subjects'] ?? '')),
        'features' => array_map('trim', explode(',', $_POST['features'] ?? '')),
        'packages' => []
    ];

    if (!empty($_POST['packages'])) {
        $packages = explode(',', $_POST['packages']);
        foreach ($packages as $pkg) {
            $pkgData = explode('|', $pkg);
            if (count($pkgData) >= 4) {
                $prices = explode(';', $pkgData[3]);
                $priceArray = [];
                foreach ($prices as $price) {
                    $priceParts = explode(':', $price);
                    if (count($priceParts) == 2) {
                        $priceArray[] = ['price_label' => $priceParts[0], 'price' => $priceParts[1]];
                    }
                }
                $data['packages'][] = [
                    'package_type' => $pkgData[0],
                    'description' => $pkgData[1],
                    'package_icon' => $pkgData[2],
                    'prices' => $priceArray,
                    'info' => $pkgData[4] ?? '',
                    'extra_info' => $pkgData[5] ?? ''
                ];
            }
        }
    }

    try {
        if ($programManager->updateProgram($data)) {
            $message = "Program berhasil diperbarui!";
            $messageType = "success";
        } else {
            $message = "Gagal memperbarui program.";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
        $messageType = "error";
    }
}

if ($section == 'program' && $action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? 0;
    try {
        if ($programManager->deleteProgram($id)) {
            $message = "Program berhasil dihapus!";
            $messageType = "success";
        } else {
            $message = "Gagal menghapus program.";
            $messageType = "error";
        }
    } catch (Exception $e) {
        $message = "Error: " . htmlspecialchars($e->getMessage());
        $messageType = "error";
    }
}

// Ambil semua pendaftaran
$pendaftaran = $registrationManager->getAllRegistrations();

// Ambil data program untuk perhitungan harga
$programs = $programManager->getAllPrograms();
$programData = [];
foreach ($programs as $program) {
    $jenjang = strtolower($program['category']);
    $programData[$jenjang] = [
        'name' => $program['title'],
        'description' => $program['description'],
        'subjects' => $program['subjects'] ?? [],
        'packages' => []
    ];
    foreach ($program['packages'] as $package) {
        $packageKey = strtolower(str_replace([' ', '-', '/'], '_', $package['package_type']));
        $programData[$jenjang]['packages'][$packageKey] = [
            'name' => $package['package_type'],
            'description' => $package['description'],
            'prices' => [],
            'additional' => $package['extra_info'] ?? ''
        ];
        foreach ($package['prices'] as $price) {
            $label = match (strtolower($price['price_label'])) {
                '8x pertemuan' => '8x',
                '12x pertemuan' => '12x',
                'harian' => 'harian',
                default => strtolower(str_replace([':', ' ', 'Pertemuan'], '', $price['price_label']))
            };
            $programData[$jenjang]['packages'][$packageKey]['prices'][$label] = $price['price'];
        }
    }
}

// Fungsi untuk format mata uang
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.') . '';
}

// Fungsi untuk menghitung harga pendaftaran
function calculateRegistrationPrice($registration, $programData) {
    $jenjang = strtolower($registration['jenjang']);
    $packageType = $registration['package_type'];
    
    $packageKey = strtolower(str_replace([' ', '-', '/'], '_', $packageType));
    
    $durasi = match (strtolower($registration['durasi'])) {
        '8x pertemuan' => '8x',
        '12x pertemuan' => '12x',
        'harian' => 'harian',
        default => strtolower(str_replace(' ', '', $registration['durasi']))
    };
    
    $jumlahHari = $registration['durasi'] === 'harian' ? (int)($registration['jumlah_hari'] ?? 1) : 1;
    $subjects = $registration['subjects'] ?? [];

    if (!isset($programData[$jenjang]['packages'][$packageKey]['prices'][$durasi])) {
        return [
            'total' => 0,
            'breakdown' => "Data harga tidak ditemukan untuk jenjang '$jenjang', paket '$packageKey', durasi '$durasi'. Available packages: " . implode(', ', array_keys($programData[$jenjang]['packages'] ?? []))
        ];
    }

    $basePricePerSubject = $programData[$jenjang]['packages'][$packageKey]['prices'][$durasi];
    $subjectCount = count($subjects);

    $totalBasePrice = $durasi === 'harian'
        ? $basePricePerSubject * $subjectCount * $jumlahHari
        : $basePricePerSubject * $subjectCount;

    $transportCost = 0;
    
    // Perbaikan: Cek berbagai kemungkinan nama paket untuk transportasi
    $transportPackageKeys = [
        'kelas_private_luar_petung_girimukti',
        'private_luar_petung_girimukti',
        'luar_petung_girimukti',
        'private_luar',
        'kelas_private_luar'
    ];
    
    $isTransportPackage = false;
    foreach ($transportPackageKeys as $key) {
        if (strpos($packageKey, $key) !== false || $packageKey === $key) {
            $isTransportPackage = true;
            break;
        }
    }

    if ($isTransportPackage) {
        if ($durasi === 'harian') {
            $transportCost = 6250 * $jumlahHari * $subjectCount;
        } else {
            $sessions = $durasi === '8x' ? 8 : 12;
            $transportCost = 6250 * $sessions * $subjectCount;
        }
    } else {
    }

    $totalPrice = $totalBasePrice + $transportCost;

    $breakdown = [
        'Mata Pelajaran' => implode(', ', $subjects),
        'Jumlah Mata Pelajaran' => $subjectCount,
        'Biaya per Mata Pelajaran' => formatCurrency($basePricePerSubject),
        'Total Biaya Pembelajaran' => formatCurrency($totalBasePrice),
        'Biaya Transportasi' => $transportCost > 0 ? formatCurrency($transportCost) : 'Tidak ada',
        'Total' => formatCurrency($totalPrice)
    ];

    return [
        'total' => $totalPrice,
        'breakdown' => $breakdown
    ];
}



// Ambil data rekomendasi belajar terbaru setiap siswa
$recommendationQuery = "SELECT 
        s.id AS student_id,
        s.nama,
        s.email,
        s.jenjang,
        s.kelas,
        ld.id AS learning_id,
        ld.mata_pelajaran,
        ld.nilai,
        ld.tingkat_kesulitan,
        ld.gaya_belajar,
        ld.catatan,
        ld.tanggal_input
    FROM students s
    LEFT JOIN learning_data ld ON ld.id = (
        SELECT ld2.id
        FROM learning_data ld2
        WHERE ld2.student_id = s.id
        ORDER BY ld2.tanggal_input DESC, ld2.id DESC
        LIMIT 1
    )
    ORDER BY s.nama ASC";
$learningRecommendations = $pdo->query($recommendationQuery)->fetchAll(PDO::FETCH_ASSOC);

function getLearningRecommendation($nilai, $tingkatKesulitan) {
    if ($nilai === null) {
        return [
            'rekomendasi' => 'Belum Ada Data',
            'keterangan' => 'Siswa belum menginput data belajar.',
            'class' => 'pending'
        ];
    }

    $nilai = (int)$nilai;

    if ($nilai < 60 || $tingkatKesulitan === 'Sulit') {
        return [
            'rekomendasi' => 'Pendampingan Intensif',
            'keterangan' => 'Nilai masih rendah atau siswa mengalami kesulitan belajar.',
            'class' => 'danger'
        ];
    }

    if ($nilai >= 60 && $nilai < 80) {
        return [
            'rekomendasi' => 'Program Intensif',
            'keterangan' => 'Siswa perlu peningkatan pemahaman melalui program intensif.',
            'class' => 'warning'
        ];
    }

    return [
        'rekomendasi' => 'Program Reguler / Pengayaan',
        'keterangan' => 'Siswa memiliki pemahaman yang baik dan dapat mengikuti pengayaan.',
        'class' => 'success'
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Bimbingan Belajar Peta Ilmu</title>
    <link rel="stylesheet" href="admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Dashboard</h2>
                <p class="sidebar-subtitle">Bimbingan Belajar Peta Ilmu</p>
            </div>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php?section=dashboard" class="<?php echo $section == 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="admin_dashboard.php?section=profil" class="<?php echo $section == 'profil' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Kelola Profil</a></li>
                    <li><a href="admin_dashboard.php?section=program" class="<?php echo $section == 'program' ? 'active' : ''; ?>"><i class="fas fa-book"></i> Kelola Program</a></li>
                    <li><a href="admin_dashboard.php?section=registration" class="<?php echo $section == 'registration' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Kelola Pendaftaran</a></li>
                    <li><a href="admin_dashboard.php?section=rekomendasi" class="<?php echo $section == 'rekomendasi' ? 'active' : ''; ?>"><i class="fas fa-lightbulb"></i> Rekomendasi Belajar</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="main-header">
                <button class="hamburger" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <div class="header-content">
                    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
                    <p class="welcome-text">Kelola profil, program dan pendaftaran dengan mudah</p>
                </div>
            </header>
            <div class="content-body">
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo htmlspecialchars($messageType); ?> fade-in">
                        <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($section == 'profil'): ?>
                    <?php
                    $organisasi_info = $organisasi_info ?? ['visi' => '', 'tahun_berdiri' => '', 'jumlah_siswa_awal' => ''];
                    $misi_list = $misi_list ?? [];
                    $sejarah_paragraf = $sejarah_paragraf ?? [];
                    $nilai_nilai = $nilai_nilai ?? [];
                    $struktur_organisasi = $struktur_organisasi ?? [];
                    $tim_pengajar = $tim_pengajar ?? [];
                    $mata_pelajaran_filter = $mata_pelajaran_filter ?? [];
                    $kontak_info = $kontak_info ?? ['alamat' => [], 'telepon' => []];
                    ?>
                    <h3>Kelola Profil</h3>
                    <form action="admin_dashboard.php?section=profil&action=update" method="POST">
                        <div class="form-group">
                            <label for="visi">Visi</label>
                            <textarea id="visi" name="visi" class="form-control" required><?php echo htmlspecialchars($organisasi_info['visi']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="misi">Misi (pisahkan dengan koma untuk beberapa item)</label>
                            <textarea id="misi" name="misi" class="form-control"><?php echo htmlspecialchars(implode(', ', $misi_list)); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tahun_berdiri">Tahun Berdiri</label>
                            <input type="text" id="tahun_berdiri" name="tahun_berdiri" class="form-control" value="<?php echo htmlspecialchars($organisasi_info['tahun_berdiri']); ?>" required />
                        </div>
                        <div class="form-group">
                            <label for="jumlah_siswa_awal">Jumlah Siswa Awal</label>
                            <input type="text" id="jumlah_siswa_awal" name="jumlah_siswa_awal" class="form-control" value="<?php echo htmlspecialchars($organisasi_info['jumlah_siswa_awal']); ?>" required />
                        </div>
                        <div class="form-group">
                            <label for="sejarah">Sejarah (pisahkan paragraf dengan koma)</label>
                            <textarea id="sejarah" name="sejarah" class="form-control"><?php echo htmlspecialchars(implode(', ', $sejarah_paragraf)); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="nilai_nilai">Nilai-Nilai (format: nama|icon|deskripsi, pisahkan item dengan koma)</label>
                            <textarea id="nilai_nilai" name="nilai_nilai" class="form-control"><?php
                                $nilai_str = '';
                                foreach ($nilai_nilai as $nilai) {
                                    $nilai_str .= $nilai['nama'] . '|' . $nilai['icon'] . '|' . $nilai['deskripsi'] . ',';
                                }
                                echo htmlspecialchars(rtrim($nilai_str, ','));
                            ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="struktur_organisasi">Struktur Organisasi (format: nama|posisi|foto|deskripsi|level, pisahkan item dengan koma)</label>
                            <textarea id="struktur_organisasi" name="struktur_organisasi" class="form-control"><?php
                                $struktur_str = '';
                                foreach ($struktur_organisasi as $staff) {
                                    $struktur_str .= $staff['nama'] . '|' . $staff['posisi'] . '|' . $staff['foto'] . '|' . $staff['deskripsi'] . '|' . $staff['level'] . ',';
                                }
                                echo htmlspecialchars(rtrim($struktur_str, ','));
                            ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tim_pengajar">Tim Pengajar (format: nama|foto|mata_pelajaran|mata_pelajaran_kode|deskripsi, pisahkan item dengan koma)</label>
                            <textarea id="tim_pengajar" name="tim_pengajar" class="form-control"><?php
                                $pengajar_str = '';
                                foreach ($tim_pengajar as $pengajar) {
                                    $pengajar_str .= $pengajar['nama'] . '|' . $pengajar['foto'] . '|' . $pengajar['mata_pelajaran'] . '|' . $pengajar['mata_pelajaran_kode'] . '|' . $pengajar['deskripsi'] . ',';
                                }
                                echo htmlspecialchars(rtrim($pengajar_str, ','));
                            ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="mata_pelajaran_filter">Filter Mata Pelajaran (format: kode|nama, pisahkan item dengan koma)</label>
                            <textarea id="mata_pelajaran_filter" name="mata_pelajaran_filter" class="form-control"><?php
                                $filter_str = '';
                                foreach ($mata_pelajaran_filter as $key => $value) {
                                    $filter_str .= $key . '|' . $value . ',';
                                }
                                echo htmlspecialchars(rtrim($filter_str, ','));
                            ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat (pisahkan dengan koma)</label>
                            <textarea id="alamat" name="alamat" class="form-control"><?php echo htmlspecialchars(implode(', ', $kontak_info['alamat'])); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="telepon">Telepon (pisahkan dengan koma)</label>
                            <textarea id="telepon" name="telepon" class="form-control"><?php echo htmlspecialchars(implode(', ', $kontak_info['telepon'])); ?></textarea>
                        </div>
                        <button type="submit" class="action-btn edit-btn">Simpan</button>
                    </form>
                <?php elseif ($section == 'program'): ?>
                    <div class="content-section fade-in">
                        <div class="section-header">
                            <h3>Kelola Program</h3>
                        </div>
                        <div class="section-body">
                            <form action="admin_dashboard.php?section=program&action=add" method="POST">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="title">Nama Program <span class="required">*</span></label>
                                        <input type="text" id="title" name="title" class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Jenjang <span class="required">*</span></label>
                                        <select id="category" name="category" class="form-control" required>
                                            <option value="sd">SD</option>
                                            <option value="smp">SMP</option>
                                            <option value="sma">SMA</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Deskripsi <span class="required">*</span></label>
                                        <textarea id="description" name="description" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="duration">Durasi <span class="required">*</span></label>
                                        <input type="text" id="duration" name="duration" class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="frequency">Frekuensi <span class="required">*</span></label>
                                        <input type="text" id="frequency" name="frequency" class="form-control" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="icon">Ikon (Font Awesome class)</label>
                                        <input type="text" id="icon" name="icon" class="form-control" value="fas fa-book" />
                                    </div>
                                    <div class="form-group">
                                        <label for="subjects">Mata Pelajaran (pisahkan dengan koma) <span class="required">*</span></label>
                                        <textarea id="subjects" name="subjects" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="features">Fitur Program (pisahkan dengan koma)</label>
                                        <textarea id="features" name="features" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="packages">Paket (format: type|description|icon|prices|info|extra_info, pisahkan item dengan koma, prices format: label:price;label:price)</label>
                                        <textarea id="packages" name="packages" class="form-control"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="primary-btn">Tambah Program</button>
                            </form>
                            <div class="table-container" style="margin-top: 30px;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Program</th>
                                            <th>Jenjang</th>
                                            <th>Deskripsi</th>
                                            <th>Durasi</th>
                                            <th>Frekuensi</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Fitur</th>
                                            <th>Paket</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($programs as $program): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($program['title']); ?></td>
                                                <td><?php echo htmlspecialchars($program['category']); ?></td>
                                                <td><?php echo htmlspecialchars($program['description']); ?></td>
                                                <td><?php echo htmlspecialchars($program['duration']); ?></td>
                                                <td><?php echo htmlspecialchars($program['frequency']); ?></td>
                                                <td><?php echo htmlspecialchars(implode(', ', $program['subjects'])); ?></td>
                                                <td><?php echo htmlspecialchars(implode(', ', $program['features'])); ?></td>
                                                <td>
                                                    <ul class="packages-list">
                                                        <?php foreach ($program['packages'] as $package): ?>
                                                            <li>
                                                                <strong><?php echo htmlspecialchars($package['package_type']); ?></strong>: 
                                                                <?php echo htmlspecialchars($package['description']); ?>
                                                                <ul class="prices-list">
                                                                    <?php foreach ($package['prices'] as $price): ?>
                                                                        <li><?php echo htmlspecialchars($price['price_label']) . ': ' . formatCurrency($price['price']); ?></li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                                <?php if (!empty($package['info'])): ?>
                                                                    <small><?php echo htmlspecialchars($package['info']); ?></small><br>
                                                                <?php endif; ?>
                                                                <?php if (!empty($package['extra_info'])): ?>
                                                                    <small><?php echo htmlspecialchars($package['extra_info']); ?></small>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <form action="admin_dashboard.php?section=program&action=edit" method="GET" style="display:inline;">
                                                        <input type="hidden" name="section" value="program">
                                                        <input type="hidden" name="action" value="edit">
                                                        <input type="hidden" name="id" value="<?php echo $program['id']; ?>">
                                                        <button type="submit" class="action-btn edit-btn">Edit</button>
                                                    </form>
                                                    <form action="admin_dashboard.php?section=program&action=delete" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $program['id']; ?>">
                                                        <button type="submit" class="action-btn delete-btn">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($action == 'edit' && isset($_GET['id'])): ?>
                                <?php
                                    $program_id = $_GET['id'];
                                    $program = $programManager->getProgramById($program_id);
                                ?>
                                <div class="content-section" style="margin-top: 30px;">
                                    <div class="section-header">
                                        <h3>Edit Program</h3>
                                    </div>
                                    <div class="section-body">
                                        <form action="admin_dashboard.php?section=program&action=update" method="POST">
                                            <input type="hidden" name="id" value="<?php echo $program['id']; ?>">
                                            <input type="hidden" name="program_code" value="<?php echo htmlspecialchars($program['program_code']); ?>">
                                            <div class="form-grid">
                                                <div class="form-group">
                                                    <label for="title">Nama Program <span class="required">*</span></label>
                                                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($program['title']); ?>" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="category">Jenjang <span class="required">*</span></label>
                                                    <select id="category" name="category" class="form-control" required>
                                                        <option value="sd" <?php echo $program['category'] == 'sd' ? 'selected' : ''; ?>>SD</option>
                                                        <option value="smp" <?php echo $program['category'] == 'smp' ? 'selected' : ''; ?>>SMP</option>
                                                        <option value="sma" <?php echo $program['category'] == 'sma' ? 'selected' : ''; ?>>SMA</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Deskripsi <span class="required">*</span></label>
                                                    <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($program['description']); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="duration">Durasi <span class="required">*</span></label>
                                                    <input type="text" id="duration" name="duration" class="form-control" value="<?php echo htmlspecialchars($program['duration']); ?>" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="frequency">Frekuensi <span class="required">*</span></label>
                                                    <input type="text" id="frequency" name="frequency" class="form-control" value="<?php echo htmlspecialchars($program['frequency']); ?>" required />
                                                </div>
                                                <div class="form-group">
                                                    <label for="icon">Ikon (Font Awesome class)</label>
                                                    <input type="text" id="icon" name="icon" class="form-control" value="<?php echo htmlspecialchars($program['icon']); ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="subjects">Mata Pelajaran (pisahkan dengan koma) <span class="required">*</span></label>
                                                    <textarea id="subjects" name="subjects" class="form-control" required><?php echo htmlspecialchars(implode(', ', $program['subjects'])); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="features">Fitur Program (pisahkan dengan koma)</label>
                                                    <textarea id="features" name="features" class="form-control"><?php echo htmlspecialchars(implode(', ', $program['features'])); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="packages">Paket (format: type|description|icon|prices|info|extra_info, pisahkan item dengan koma, prices format: label:price;label:price)</label>
                                                    <textarea id="packages" name="packages" class="form-control"><?php
                                                        $packages_str = '';
                                                        foreach ($program['packages'] as $package) {
                                                            $prices_str = '';
                                                            foreach ($package['prices'] as $price) {
                                                                $prices_str .= $price['price_label'] . ':' . $price['price'] . ';';
                                                            }
                                                            $packages_str .= $package['package_type'] . '|' . $package['description'] . '|' . $package['package_icon'] . '|' . rtrim($prices_str, ';') . '|' . ($package['info'] ?? '') . '|' . ($package['extra_info'] ?? '') . ',';
                                                        }
                                                        echo htmlspecialchars(rtrim($packages_str, ','));
                                                    ?></textarea>
                                                </div>
                                            </div>
                                            <button type="submit" class="primary-btn">Simpan Perubahan</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($section == 'registration'): ?>
                    <div class="content-section fade-in">
                        <div class="section-header">
                            <h3>Kelola Pendaftaran</h3>
                        </div>
                        <div class="section-body">
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jenjang</th>
                                            <th>Kelas</th>
                                            <th>Telepon</th>
                                            <th>Email</th>
                                            <th>Paket Program</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendaftaran as $registration): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($registration['nama_lengkap']); ?></td>
                                                <td><?php echo htmlspecialchars($registration['jenjang']); ?></td>
                                                <td><?php echo htmlspecialchars($registration['kelas']); ?></td>
                                                <td><?php echo htmlspecialchars($registration['telepon']); ?></td>
                                                <td><?php echo htmlspecialchars($registration['email'] ?: '-'); ?></td>
                                                <td><?php echo htmlspecialchars($registration['package_type']); ?></td>
                                                <td><?php echo htmlspecialchars(implode(', ', $registration['subjects'])); ?></td>
                                                <td>
                                                    <form action="admin_dashboard.php?section=registration&action=update_status" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $registration['id']; ?>">
                                                        <select name="status" class="status-select" onchange="this.form.submit()">
                                                            <option value="pending" <?php echo $registration['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                            <option value="confirmed" <?php echo $registration['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                            <option value="rejected" <?php echo $registration['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form action="admin_dashboard.php?section=registration&action=delete" method="POST" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $registration['id']; ?>">
                                                        <button type="submit" class="action-btn delete-btn">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="content-section" style="margin-top: 30px;">
                                <div class="section-header">
                                    <h3>Rincian Biaya Pendaftaran</h3>
                                </div>
                                <div class="section-body">
                                    <div class="table-container">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Jenjang</th>
                                                    <th>Paket</th>
                                                    <th>Durasi</th>
                                                    <th>Jumlah Hari</th>
                                                    <th>Mata Pelajaran</th>
                                                    <th>Total Biaya</th>
                                                    <th>Rincian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($pendaftaran)): ?>
                                                    <tr>
                                                        <td colspan="8" style="text-align: center; padding: 20px;">
                                                            Tidak ada data pendaftaran ditemukan.
                                                        </td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($pendaftaran as $registration): ?>
                                                        <?php 
                                                        $priceInfo = calculateRegistrationPrice($registration, $programData);
                                                        $totalPrice = $priceInfo['total'];
                                                        ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($registration['nama_lengkap'] ?? '-'); ?></td>
                                                            <td><?php echo htmlspecialchars($registration['jenjang'] ?? '-'); ?></td>
                                                            <td><?php echo htmlspecialchars($registration['package_type'] ?? '-'); ?></td>
                                                            <td><?php echo htmlspecialchars($registration['durasi'] ?? '-'); ?></td>
                                                            <td><?php echo ($registration['durasi'] ?? '') === 'harian' ? ($registration['jumlah_hari'] ?? '1') : '-'; ?></td>
                                                            <td><?php echo htmlspecialchars(!empty($registration['subjects']) ? implode(', ', $registration['subjects']) : '-'); ?></td>
                                                            <td><?php echo formatCurrency($totalPrice); ?></td>
                                                            <td>
                                                                <?php if (is_array($priceInfo['breakdown'])): ?>
                                                                    <ul>
                                                                        <?php foreach ($priceInfo['breakdown'] as $key => $value): ?>
                                                                            <li><strong><?php echo htmlspecialchars($key); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                                                                        <?php endforeach; ?>
                                                                    </ul>
                                                                <?php else: ?>
                                                                    <?php echo htmlspecialchars($priceInfo['breakdown']); ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($section == 'rekomendasi'): ?>
                    <div class="content-section fade-in">
                        <div class="section-header">
                            <h3>Rekomendasi Belajar Siswa</h3>
                            <p>Data ini diambil dari input belajar terakhir setiap siswa.</p>
                        </div>
                        <div class="section-body">
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Jenjang</th>
                                            <th>Kelas</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Nilai</th>
                                            <th>Kesulitan</th>
                                            <th>Gaya Belajar</th>
                                            <th>Rekomendasi</th>
                                            <th>Keterangan</th>
                                            <th>Tanggal Input</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($learningRecommendations)): ?>
                                            <tr>
                                                <td colspan="11" style="text-align:center; padding:20px;">Belum ada data siswa.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($learningRecommendations as $i => $row): ?>
                                                <?php $hasil = getLearningRecommendation($row['nilai'], $row['tingkat_kesulitan']); ?>
                                                <tr>
                                                    <td><?php echo $i + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                    <td><?php echo htmlspecialchars(strtoupper($row['jenjang'])); ?></td>
                                                    <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['mata_pelajaran'] ?? '-'); ?></td>
                                                    <td><?php echo $row['nilai'] !== null ? htmlspecialchars($row['nilai']) : '-'; ?></td>
                                                    <td><?php echo htmlspecialchars($row['tingkat_kesulitan'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($row['gaya_belajar'] ?? '-'); ?></td>
                                                    <td>
                                                        <span class="rekom-badge <?php echo $hasil['class']; ?>">
                                                            <?php echo htmlspecialchars($hasil['rekomendasi']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($hasil['keterangan']); ?></td>
                                                    <td>
                                                        <?php echo !empty($row['tanggal_input']) ? date('d/m/Y H:i', strtotime($row['tanggal_input'])) : '-'; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <h3 style="margin-bottom: 20px;">Dashboard Overview</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                        <div class="content-section" style="margin-bottom: 0;">
                            <div class="section-body" style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(79, 70, 229, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-book"></i></div>
                                <div>
                                    <div style="font-size: 1.8rem; font-weight: 700; line-height: 1.25;"><?php echo count($programs); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--muted); font-weight: 500;">Total Program Belajar</div>
                                </div>
                            </div>
                        </div>
                        <div class="content-section" style="margin-bottom: 0;">
                            <div class="section-body" style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-user-plus"></i></div>
                                <div>
                                    <div style="font-size: 1.8rem; font-weight: 700; line-height: 1.25;"><?php echo count($pendaftaran); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--muted); font-weight: 500;">Total Pendaftaran Siswa</div>
                                </div>
                            </div>
                        </div>
                        <div class="content-section" style="margin-bottom: 0;">
                            <div class="section-body" style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;"><i class="fas fa-lightbulb"></i></div>
                                <div>
                                    <div style="font-size: 1.8rem; font-weight: 700; line-height: 1.25;"><?php echo count(array_filter($learningRecommendations, fn($r) => $r['learning_id'] !== null)); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--muted); font-weight: 500;">Rekomendasi Belajar Aktif</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;

            hamburger.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-open');
                body.classList.toggle('sidebar-active');
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('sidebar-open')) {
                    if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
                        sidebar.classList.remove('sidebar-open');
                        body.classList.remove('sidebar-active');
                    }
                }
            });
        });
    </script>
</body>
</html>