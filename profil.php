<?php
require_once 'config/database.php';
require_once 'classes/ProfilManager.php';

$page_title = "Profil - Bimbingan Belajar Peta Ilmu";
$current_page = "profil";

$database = new Database();
$pdo = $database->getConnection();
$profilManager = new ProfilManager($pdo);

try {
    $organisasi_info = $profilManager->getOrganisasiInfo();
    $sejarah_paragraf = $profilManager->getSejarahOrganisasi();
    $misi_list = $profilManager->getMisiOrganisasi();
    $nilai_nilai = $profilManager->getNilaiOrganisasi();
    $struktur_organisasi = $profilManager->getStrukturOrganisasi();
    $tim_pengajar = $profilManager->getTimPengajar();
    $mata_pelajaran_filter = $profilManager->getMataPelajaranFilter();
    $kontak_info = $profilManager->getKontakInfo();
    
    $sejarah = [
        "tahun_berdiri" => $organisasi_info['tahun_berdiri'] ?? '2024',
        "jumlah_siswa_awal" => $organisasi_info['jumlah_siswa_awal'] ?? '5',
        "deskripsi" => $sejarah_paragraf ?: ['Data tidak dapat dimuat dari database.']
    ];
    
    $visi = $organisasi_info['visi'] ?? 'Data tidak dapat dimuat dari database.';
    $misi = $misi_list ?: ['Data tidak dapat dimuat dari database.'];
    
} catch (Exception $e) {
    error_log('Profil Page Error: ' . $e->getMessage());
    $sejarah = [
        "tahun_berdiri" => "2024",
        "jumlah_siswa_awal" => "5",
        "deskripsi" => ["Data tidak dapat dimuat dari database."]
    ];
    $visi = "Data tidak dapat dimuat dari database.";
    $misi = ["Data tidak dapat dimuat dari database."];
    $nilai_nilai = [];
    $struktur_organisasi = [];
    $tim_pengajar = [];
    $mata_pelajaran_filter = ['all' => 'Semua'];
    $kontak_info = ['alamat' => [], 'telepon' => [], 'email' => [], 'fax' => []];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="styleprofil.css" />
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
                        <li><a href="profil.php" class="<?php echo ($current_page == 'profil') ? 'active' : ''; ?>">Profil</a></li>
                        <li><a href="program.php">Program</a></li>
                        <li><a href="pendaftaran.php">Pendaftaran</a></li>
                        <li><a href="kontak.php">Kontak</a></li>
                    </ul>
                </nav>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1>Profil Lembaga</h1>
                <p>Mengenal lebih dekat Bimbingan Belajar Peta Ilmu</p>
                <nav class="breadcrumb">
                    <a href="index.php">Beranda</a>
                    <span class="separator">/</span>
                    <span class="current">Profil</span>
                </nav>
            </div>
        </div>
        <div class="page-header-bg"></div>
    </section>

    <!-- Sejarah Section -->
    <section class="sejarah-section">
        <div class="container">
            <div class="sejarah-wrapper">
                <div class="sejarah-content">
                    <div class="section-header">
                        <h2>Sejarah Peta Ilmu</h2>
                        <div class="section-divider"></div>
                    </div>
                    <div class="sejarah-text">
                        <?php foreach ($sejarah['deskripsi'] as $paragraf): ?>
                            <p><?php echo htmlspecialchars($paragraf); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="sejarah-image">
                    <img src="images/sejarah-building.jpg" alt="Peta Ilmu" />
                    <div class="image-caption">
                        <p>Peta Ilmu yang telah menjadi rumah belajar ribuan siswa</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section class="visi-misi-section">
        <div class="container">
            <div class="section-header">
                <h2>Visi & Misi</h2>
                <div class="section-divider"></div>
                <p>Landasan dan tujuan kami dalam memberikan pendidikan terbaik</p>
            </div>
            <div class="visi-misi-wrapper">
                <div class="visi-card">
                    <div class="card-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Visi</h3>
                    <p><?php echo htmlspecialchars($visi); ?></p>
                </div>
                <div class="misi-card">
                    <div class="card-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Misi</h3>
                    <ul>
                        <?php foreach ($misi as $item_misi): ?>
                            <li><?php echo htmlspecialchars($item_misi); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Nilai-nilai Section -->
    <section class="nilai-section">
        <div class="container">
            <div class="section-header">
                <h2>Nilai-Nilai Kami</h2>
                <div class="section-divider"></div>
                <p>Prinsip-prinsip yang menjadi fondasi dalam setiap aktivitas kami</p>
            </div>
            <div class="nilai-grid">
                <?php foreach ($nilai_nilai as $nilai): ?>
                    <div class="nilai-item">
                        <div class="nilai-icon">
                            <i class="<?php echo htmlspecialchars($nilai['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($nilai['nama']); ?></h3>
                        <p><?php echo htmlspecialchars($nilai['deskripsi']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Struktur Organisasi Section -->
    <section class="struktur-section">
        <div class="container">
            <div class="section-header">
                <h2>Struktur Organisasi</h2>
                <div class="section-divider"></div>
                <p>Tim manajemen bimbingan belajar Peta Ilmu</p>
            </div>
            <div class="struktur-chart">
                <?php 
                $levels = [1, 2, 3];
                foreach ($levels as $level): 
                    $staff_level = array_filter($struktur_organisasi, function($staff) use ($level) {
                        return $staff['level'] == $level;
                    });
                    if (!empty($staff_level)):
                ?>
                    <div class="struktur-level level-<?php echo $level; ?>">
                        <?php foreach ($staff_level as $staff): ?>
                            <div class="struktur-card">
                                <div class="struktur-photo">
                                    <img src="<?php echo htmlspecialchars($staff['foto']); ?>" 
                                         alt="<?php echo htmlspecialchars($staff['posisi']); ?>" />
                                </div>
                                <div class="struktur-info">
                                    <h3><?php echo htmlspecialchars($staff['nama']); ?></h3>
                                    <span class="position"><?php echo htmlspecialchars($staff['posisi']); ?></span>
                                    <?php if (!empty($staff['deskripsi'])): ?>
                                        <p><?php echo htmlspecialchars($staff['deskripsi']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </section>

    <!-- Tim Pengajar Section -->
    <section class="pengajar-section" id="pengajar">
        <div class="container">
            <div class="section-header">
                <h2>Tim Pengajar Profesional</h2>
                <div class="section-divider"></div>
                <p>Tenaga pengajar berpengalaman dan berkualitas yang siap membimbing kesuksesanmu</p>
            </div>

            <!-- Filter Mata Pelajaran -->
            <div class="pengajar-filter">
                <?php foreach ($mata_pelajaran_filter as $key => $value): ?>
                    <button class="filter-btn <?php echo ($key == 'all') ? 'active' : ''; ?>" 
                            data-subject="<?php echo htmlspecialchars($key); ?>">
                        <?php echo htmlspecialchars($value); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="pengajar-grid">
                <?php foreach ($tim_pengajar as $pengajar): ?>
                    <div class="pengajar-card" 
                         data-subject="<?php echo htmlspecialchars($pengajar['mata_pelajaran_kode']); ?>">
                        <div class="pengajar-photo">
                            <img src="<?php echo htmlspecialchars($pengajar['foto']); ?>" 
                                 alt="Guru <?php echo htmlspecialchars($pengajar['mata_pelajaran']); ?>" />
                        </div>
                        <div class="pengajar-info">
                            <h3><?php echo htmlspecialchars($pengajar['nama']); ?></h3>
                            <span class="subject"><?php echo htmlspecialchars($pengajar['mata_pelajaran']); ?></span>
                            <p><?php echo htmlspecialchars($pengajar['deskripsi']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pengajar-cta">
                <a href="program.php" class="btn btn-primary">Lihat Program Pembelajaran</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Bergabunglah dengan Keluarga Besar Peta Ilmu!</h2>
                <p>Dapatkan bimbingan terbaik dari tim pengajar profesional kami untuk meraih prestasi akademik yang gemilang.</p>
                <div class="cta-buttons">
                    <a href="pendaftaran.php" class="btn btn-primary">Daftar Sekarang</a>
                    <a href="kontak.php" class="btn btn-secondary">Hubungi Kami</a>
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
                        <?php if (!empty($kontak_info['alamat'])): ?>
                            <?php foreach ($kontak_info['alamat'] as $alamat): ?>
                                <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($alamat); ?></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($kontak_info['telepon'])): ?>
                            <?php foreach ($kontak_info['telepon'] as $telepon): ?>
                                <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($telepon); ?></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Bimbingan Belajar Peta Ilmu. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript untuk filter pengajar -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const pengajarCards = document.querySelectorAll('.pengajar-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const selectedSubject = this.getAttribute('data-subject');

                pengajarCards.forEach(card => {
                    if (selectedSubject === 'all' || card.getAttribute('data-subject') === selectedSubject) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>
</body>
</html>