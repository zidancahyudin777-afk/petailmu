<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: siswa_login.php');
    exit;
}

require_once 'classes/StudentManager.php';
$studentManager = new StudentManager();

$student = $studentManager->getStudentById($_SESSION['student_id']);
if (!$student) {
    header('Location: siswa_logout.php');
    exit;
}

$registrations = $studentManager->getRegistrationsByStudentId($_SESSION['student_id']);

function statusLabel($status) {
    return match($status) {
        'confirmed' => ['label' => 'Dikonfirmasi', 'class' => 'confirmed', 'icon' => 'fa-check-circle'],
        'rejected'  => ['label' => 'Ditolak', 'class' => 'rejected', 'icon' => 'fa-times-circle'],
        default     => ['label' => 'Menunggu', 'class' => 'pending', 'icon' => 'fa-clock'],
    };
}

function rupiahFormat($amount) {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Siswa - Bimbingan Belajar Peta Ilmu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="siswa_portal.css" />
</head>
<body>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar">

    <div class="sidebar-brand">
        <img src="images/IMG_3898.PNG" alt="Logo Peta Ilmu" />
        <h2>Peta Ilmu</h2>
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

        <a href="siswa_dashboard.php" class="active">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="pendaftaran.php">
            <i class="fas fa-user-plus"></i> Daftar Program
        </a>

        <a href="input_data_belajar.php">
            <i class="fas fa-book"></i> Input Data Belajar
        </a>

        <a href="perkembangan_belajar.php">
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
            <i class="fas fa-tachometer-alt" style="color:var(--primary);margin-right:6px;"></i>
            Dashboard Siswa
        </h1>
        <a href="index.php" class="site-link">
            <i class="fas fa-external-link-alt"></i> Ke Website Utama
        </a>
    </div>

    <div class="page-body">

        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <span>
                Selamat datang di <strong>Portal Siswa Peta Ilmu</strong>. 
                Di sini Anda dapat memantau status pendaftaran program bimbingan belajar Anda.
            </span>
        </div>

        <div class="greeting-card">
            <div>
                <h2>Halo, <?php echo htmlspecialchars(explode(' ', $student['nama'])[0]); ?>! 👋</h2>
                <p>
                    Jenjang: <?php echo jenjangLabel($student['jenjang']); ?> &bull;
                    Kelas <?php echo htmlspecialchars($student['kelas']); ?> &bull;
                    Bergabung sejak <?php echo date('d M Y', strtotime($student['created_at'])); ?>
                </p>
            </div>
            <div class="greet-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
        </div>

        <?php
            $totalPendaftaran = count($registrations);
            $totalConfirmed = count(array_filter($registrations, fn($r) => $r['status'] === 'confirmed'));
            $totalPending = count(array_filter($registrations, fn($r) => $r['status'] === 'pending'));
            $totalBiaya = array_sum(array_column($registrations, 'total_price'));
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-file-alt"></i></div>
                <div class="stat-info">
                    <div class="val"><?php echo $totalPendaftaran; ?></div>
                    <div class="lbl">Total Pendaftaran</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <div class="val"><?php echo $totalConfirmed; ?></div>
                    <div class="lbl">Dikonfirmasi</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="val"><?php echo $totalPending; ?></div>
                    <div class="lbl">Menunggu Konfirmasi</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-wallet"></i></div>
                <div class="stat-info">
                    <div class="val" style="font-size:1rem;"><?php echo rupiahFormat($totalBiaya); ?></div>
                    <div class="lbl">Total Biaya</div>
                </div>
            </div>
        </div>

        <div class="cards-row">

            <div class="card">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-user"></i></div>
                    <h3>Profil Saya</h3>
                </div>
                <div class="card-body">
                    <div class="profile-avatar">
                        <?php echo strtoupper(mb_substr($student['nama'], 0, 1)); ?>
                    </div>
                    <div class="profile-name"><?php echo htmlspecialchars($student['nama']); ?></div>
                    <div class="profile-email"><?php echo htmlspecialchars($student['email']); ?></div>

                    <div class="pf-row">
                        <span class="pf-label">Jenjang</span>
                        <span class="pf-val"><?php echo jenjangLabel($student['jenjang']); ?></span>
                    </div>

                    <div class="pf-row">
                        <span class="pf-label">Kelas</span>
                        <span class="pf-val">Kelas <?php echo htmlspecialchars($student['kelas']); ?></span>
                    </div>

                    <div class="pf-row">
                        <span class="pf-label">Terdaftar</span>
                        <span class="pf-val"><?php echo date('d/m/Y', strtotime($student['created_at'])); ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-list-check"></i></div>
                    <h3>Riwayat Pendaftaran Program</h3>
                </div>

                <div class="card-body" style="padding:0;">
                    <?php if (empty($registrations)): ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Belum ada pendaftaran program.<br>Daftar sekarang untuk mulai belajar!</p>
                            <a href="pendaftaran.php">
                                <i class="fas fa-user-plus"></i> Daftar Program
                            </a>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenjang</th>
                                    <th>Paket</th>
                                    <th>Durasi</th>
                                    <th>Total Biaya</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $i => $reg): ?>
                                    <?php $s = statusLabel($reg['status']); ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo jenjangLabel($reg['jenjang']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['package_type']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['durasi']); ?></td>
                                        <td><?php echo rupiahFormat($reg['total_price']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $s['class']; ?>">
                                                <i class="fas <?php echo $s['icon']; ?>"></i>
                                                <?php echo $s['label']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($reg['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
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