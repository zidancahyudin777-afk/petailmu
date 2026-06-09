<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: siswa_login.php");
    exit;
}

require_once __DIR__ . '/classes/LearningDataManager.php';
require_once __DIR__ . '/classes/StudentManager.php';

$learningManager = new LearningDataManager();
$studentManager = new StudentManager();

$student = $studentManager->getStudentById($_SESSION['student_id']);
if (!$student) {
    header('Location: siswa_logout.php');
    exit;
}

$dataBelajar = $learningManager->getLearningDataByStudent($_SESSION['student_id']);

$totalData = count($dataBelajar);
$totalNilai = 0;
$nilaiTerakhir = 0;
$mapelTerakhir = "-";

foreach ($dataBelajar as $data) {
    $totalNilai += $data['nilai'];
}

if ($totalData > 0) {
    $rataRata = $totalNilai / $totalData;
    $nilaiTerakhir = $dataBelajar[0]['nilai'];
    $mapelTerakhir = $dataBelajar[0]['mata_pelajaran'];
} else {
    $rataRata = 0;
}

if ($rataRata >= 80) {
    $statusClass = "success";
    $statusText = "Perkembangan belajar baik";
    $keterangan = "Siswa menunjukkan hasil belajar yang sangat baik. Pertahankan konsistensi dan intensitas belajar Anda!";
} elseif ($rataRata >= 60) {
    $statusClass = "warning";
    $statusText = "Perkembangan belajar cukup";
    $keterangan = "Siswa sudah cukup memahami materi dengan baik, namun disarankan melakukan latihan soal tambahan untuk memperkuat konsep.";
} else {
    $statusClass = "danger";
    $statusText = "Perlu peningkatan belajar";
    $keterangan = "Siswa disarankan mendapatkan pendampingan atau bimbingan belajar khusus agar pemahaman konsep akademik meningkat.";
}

function jenjangLabel($j) {
    return match(strtolower($j)) {
        'sd'  => 'SD/MI',
        'smp' => 'SMP/MTs',
        'sma' => 'SMA/MA/SMK',
        default => strtoupper($j),
    };
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perkembangan Belajar - Portal Siswa Peta Ilmu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="siswa_portal.css" />
    <style>
        .custom-status-box {
            padding: 24px;
            border-radius: var(--radius);
            margin-bottom: 28px;
            border-left: 6px solid var(--primary);
            box-shadow: var(--box-shadow-sm);
        }
        .custom-status-box.success { border-color: var(--success); background-color: #ecfdf5; color: #065f46; }
        .custom-status-box.warning { border-color: var(--warning); background-color: #fef3c7; color: #92400e; }
        .custom-status-box.danger { border-color: var(--danger); background-color: #fef2f2; color: #991b1b; }
        
        .custom-status-box h3 {
            font-size: 1.2rem;
            margin-bottom: 6px;
            font-weight: 700;
            color: inherit;
        }
        .custom-status-box p {
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 0;
            opacity: 0.9;
        }
    </style>
</head>
<body>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="siswa_dashboard.php">
            <img src="images/IMG_3898.PNG" alt="Logo Peta Ilmu" />
            <h2>Peta Ilmu</h2>
        </a>
        <p>Portal Siswa</p>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?php echo strtoupper(mb_substr($student['nama'], 0, 1)); ?>
        </div>
        <div class="user-info">
            <div class="name"><?php echo htmlspecialchars($student['nama']); ?></div>
            <div class="role">
                <?php echo jenjangLabel($student['jenjang']); ?> &bull; 
                Kelas <?php echo htmlspecialchars($student['kelas']); ?>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>

        <a href="siswa_dashboard.php">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="pendaftaran.php">
            <i class="fas fa-user-plus"></i> Daftar Program
        </a>

        <a href="input_data_belajar.php">
            <i class="fas fa-book"></i> Input Data Belajar
        </a>

        <a href="perkembangan_belajar.php" class="active">
            <i class="fas fa-chart-line"></i> Perkembangan Belajar
        </a>
        <a href="rekomendasi_belajar.php">
            <i class="fas fa-lightbulb"></i> Rekomendasi Belajar
        </a>
        <div class="nav-label" style="margin-top:8px;">Informasi</div>

        <a href="program.php">
            <i class="fas fa-book-open"></i> Lihat Program
        </a>

        <a href="kontak.php">
            <i class="fas fa-phone"></i> Kontak Kami
        </a>

        <a href="index.php">
            <i class="fas fa-globe"></i> Kembali ke Website
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="siswa_logout.php" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1>
            <i class="fas fa-chart-line" style="color:var(--primary);margin-right:6px;"></i>
            Pemantauan Perkembangan Belajar
        </h1>
        <a href="siswa_dashboard.php" class="site-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="page-body">
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <span>
                Berikut adalah rangkuman performa dan aktivitas belajar Anda yang didapatkan dari riwayat input berkala. 
                Pantau nilai rata-rata Anda agar tetap berada pada kategori optimal.
            </span>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-folder-open"></i></div>
                <div class="stat-info">
                    <div class="val"><?php echo $totalData; ?></div>
                    <div class="lbl">Total Data Belajar</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-graduation-cap"></i></div>
                <div class="stat-info">
                    <div class="val"><?php echo number_format($rataRata, 1); ?></div>
                    <div class="lbl">Rata-rata Nilai</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-star"></i></div>
                <div class="stat-info">
                    <div class="val"><?php echo $nilaiTerakhir; ?></div>
                    <div class="lbl">Nilai Terakhir</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-book-open"></i></div>
                <div class="stat-info">
                    <div class="val" style="font-size: 1.15rem; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; max-width: 130px;"><?php echo htmlspecialchars($mapelTerakhir); ?></div>
                    <div class="lbl">Mapel Terakhir</div>
                </div>
            </div>
        </div>

        <!-- Status Card (Decision Analysis) -->
        <div class="custom-status-box <?php echo $statusClass; ?>">
            <h3><?php echo $statusText; ?></h3>
            <p><?php echo $keterangan; ?></p>
        </div>

        <!-- History Card -->
        <div class="card">
            <div class="card-header">
                <div class="header-icon"><i class="fas fa-list-ul"></i></div>
                <h3>Riwayat Perkembangan Belajar</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Mata Pelajaran</th>
                                <th>Nilai</th>
                                <th>Kesulitan</th>
                                <th>Gaya Belajar</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($totalData > 0): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($dataBelajar as $data): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($data['mata_pelajaran']); ?></strong></td>
                                        <td>
                                            <span style="font-weight: 700; color: <?php echo $data['nilai'] >= 80 ? 'var(--success)' : ($data['nilai'] >= 60 ? 'var(--warning)' : 'var(--danger)'); ?>">
                                                <?php echo htmlspecialchars($data['nilai']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $data['tingkat_kesulitan'] === 'Mudah' ? 'confirmed' : ($data['tingkat_kesulitan'] === 'Sedang' ? 'pending' : 'rejected'); ?>">
                                                <?php echo htmlspecialchars($data['tingkat_kesulitan']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($data['gaya_belajar']); ?></td>
                                        <td><small class="text-muted"><?php echo date('d M Y', strtotime($data['tanggal_input'])); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Belum ada data perkembangan belajar.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        if (toggleBtn && sidebar && backdrop) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                backdrop.classList.toggle('active');
            });
            
            backdrop.addEventListener('click', () => {
                sidebar.classList.remove('open');
                backdrop.classList.remove('active');
            });
        }
    });
</script>
</body>
</html>