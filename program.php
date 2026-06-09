<?php
// program.php - Program Pembelajaran Page
require_once 'config/database.php';
require_once 'classes/ProgramManager.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $programManager = new ProgramManager($pdo);
    $programs = $programManager->getAllPrograms();
    $benefits = $programManager->getProgramBenefits();
    $faqs = $programManager->getProgramFAQs();
} catch (Exception $e) {
    error_log("Database error in program.php: " . $e->getMessage());
    $programs = $programManager->getFallbackPrograms();
    $benefits = $programManager->getFallbackBenefits();
    $faqs = $programManager->getFallbackFAQs();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Program Pembelajaran - Bimbingan Belajar Peta Ilmu</title>
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="styleprogram.css" />
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
                        <li><a href="program.php" class="active">Program</a></li>
                        <li><a href="pendaftaran.php">Pendaftaran</a></li>
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

    <!-- Program Hero Section -->
    <section class="program-hero">
        <div class="container">
            <h1>Program Pembelajaran</h1>
            <p>
                Pilih program pembelajaran yang sesuai dengan kebutuhan dan jenjang
                pendidikan Anda. Kami menyediakan berbagai program berkualitas tinggi
                dengan metode pembelajaran yang efektif.
            </p>
        </div>
    </section>

    <!-- Program Filter & Grid -->
    <section class="program-filter">
        <div class="container">
            <div class="filter-buttons">
                <a href="#" class="filter-btn active" data-filter="all">Semua Program</a>
                <a href="#sd" class="filter-btn" data-filter="sd">SD</a>
                <a href="#smp" class="filter-btn" data-filter="smp">SMP</a>
                <a href="#sma" class="filter-btn" data-filter="sma">SMA</a>
            </div>

            <div class="program-grid">
                <?php if (empty($programs)): ?>
                    <div class="no-programs">
                        <p>Program sedang tidak tersedia. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($programs as $program): ?>
                        <div class="program-card" data-category="<?php echo htmlspecialchars($program['category']); ?>" id="<?php echo htmlspecialchars($program['id']); ?>">
                            <div class="program-card-header">
                                <div class="icon">
                                    <i class="<?php echo htmlspecialchars($program['icon']); ?>"></i>
                                </div>
                            </div>
                            <div class="program-card-content">
                                <h3><?php echo htmlspecialchars($program['title']); ?></h3>
                                <p class="program-description">
                                    <?php echo htmlspecialchars($program['description']); ?>
                                </p>

                                <?php if (!empty($program['features'])): ?>
                                    <ul class="program-features">
                                        <?php foreach ($program['features'] as $feature): ?>
                                            <li><?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <div class="program-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Durasi</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($program['duration']); ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Frekuensi</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($program['frequency']); ?></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Paket Kelas</span>
                                        <span class="detail-value">Private & Reguler</span>
                                    </div>
                                </div>

                                <?php if (!empty($program['packages'])): ?>
                                    <div class="program-packages">
                                        <?php foreach ($program['packages'] as $package): ?>
                                            <div class="package-item">
                                                <h4>
                                                    <i class="<?php echo htmlspecialchars($package['package_type']); ?>"></i> 
                                                    <?php echo htmlspecialchars($package['package_type']); ?>
                                                </h4>
                                                <p><?php echo htmlspecialchars($package['description']); ?></p>
                                                
                                                <?php if (!empty($package['prices'])): ?>
                                                    <div class="price-options">
                                                        <?php foreach ($package['prices'] as $price): ?>
                                                            <div class="price-option">
                                                                <span class="option-label"><?php echo htmlspecialchars($price['price_label']); ?></span>
                                                               <span class="package-price">Rp <?php echo number_format($price['price'], 0, ',', '.'); ?></span>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($package['info'])): ?>
                                                    <p class="additional-info"><?php echo htmlspecialchars($package['info']); ?></p>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($package['extra_info'])): ?>
                                                    <p class="additional-info"><?php echo htmlspecialchars($package['extra_info']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="program-cta">
                                    <a href="pendaftaran.php" class="btn-program btn-primary">Daftar Sekarang</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Program Benefits -->
    <section class="program-benefits">
        <div class="container">
            <div class="section-header">
                <h2>Keunggulan Program Kami</h2>
                <p>Mengapa siswa dan orang tua memilih program pembelajaran di Peta Ilmu</p>
            </div>
            <div class="benefits-grid">
                <?php foreach ($benefits as $benefit): ?>
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="<?php echo htmlspecialchars($benefit['icon']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($benefit['title']); ?></h3>
                        <p><?php echo htmlspecialchars($benefit['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2>Pertanyaan yang Sering Diajukan</h2>
                <p>Temukan jawaban atas pertanyaan umum tentang program pembelajaran kami</p>
            </div>
            <div class="faq-container">
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span><?php echo htmlspecialchars($faq['question']); ?></span>
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                <?php echo htmlspecialchars($faq['answer']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
                <p>&copy; <?php echo date('Y'); ?> Bimbingan Belajar Peta Ilmu. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>