<?php
// Konfigurasi halaman
$page_title = "Kontak Kami - Bimbingan Belajar Peta Ilmu";
$current_page = "kontak";

// Data kontak
$contact_info = [
    [
        'icon' => 'fas fa-map-marker-alt',
        'title' => 'Alamat Kami',
        'content' => 'Perum Nuansa Blok.C No.9<br>Kelurahan Petung<br>Penajam Paser Utara 76143<br>Kalimantan Timur'
    ],
    [
        'icon' => 'fas fa-map-marker-alt',
        'title' => 'Alamat Kami',
        'content' => 'Girimukti Strat 3<br>Desa Girimukti<br>Penajam Paser Utara 76143<br>Kalimantan Timur'
    ],
    [
        'icon' => 'fas fa-phone',
        'title' => 'Telepon',
        'content' => '<strong>Contact Person 1 :</strong> <br/>+62 898-1792-917'
    ],
    [
        'icon' => 'fas fa-phone',
        'title' => 'Telepon',
        'content' => '<strong>Contact Person 2 :</strong> <br/>+62 822-5513-1993'
    ]
];

// Data FAQ
$faq_data = [
    [
        'question' => 'Bagaimana cara mendaftar di Peta Ilmu?',
        'answer' => 'Anda dapat mendaftar secara online melalui halaman pendaftaran di website kami, datang langsung ke kantor kami, atau menghubungi kami via telepon/WhatsApp untuk informasi lebih lanjut.'
    ],
    [
        'question' => 'Berapa jumlah siswa per kelas?',
        'answer' => 'Kami membatasi jumlah siswa maksimal 5 orang per kelas untuk memastikan setiap siswa mendapat perhatian yang optimal dari pengajar.'
    ],
    [
        'question' => 'Apakah tersedia konsultasi dengan orang tua?',
        'answer' => 'Ya, kami mengadakan sesi konsultasi rutin dengan orang tua setiap bulan untuk membahas perkembangan belajar anak. Konsultasi tambahan dapat dijadwalkan sesuai kebutuhan.'
    ]
];

// Menu navigasi
$nav_menu = [
    ['url' => 'index.php', 'text' => 'Beranda', 'active' => false],
    ['url' => 'profil.php', 'text' => 'Profil', 'active' => false],
    ['url' => 'program.php', 'text' => 'Program', 'active' => false],
    ['url' => 'pendaftaran.php', 'text' => 'Pendaftaran', 'active' => false],
    ['url' => 'kontak.php', 'text' => 'Kontak', 'active' => true],
];

// Data footer
$footer_links = [
    ['url' => 'index.php', 'text' => 'Beranda'],
    ['url' => 'profil.php', 'text' => 'Profil'],
    ['url' => 'program.php', 'text' => 'Program'],
    ['url' => 'pendaftaran.php', 'text' => 'Pendaftaran'],
    ['url' => 'kontak.php', 'text' => 'Kontak'],
];

$footer_programs = [
    ['url' => 'program.php#sd', 'text' => 'Program SD'],
    ['url' => 'program.php#smp', 'text' => 'Program SMP'],
    ['url' => 'program.php#sma', 'text' => 'Program SMA'],
];

$footer_contacts = [
    ['icon' => 'fas fa-map-marker-alt', 'text' => 'Perumahan Nuansa Petung Blok C No.9'],
    ['icon' => 'fas fa-map-marker-alt', 'text' => 'Girimukti Rt.10 No.55 Strat 3'],
    ['icon' => 'fas fa-phone', 'text' => '+62 8981792917'],
    ['icon' => 'fas fa-phone', 'text' => '+62 82255131993'],
];


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="stylekontak.css" />
    <script src="universal.js" defer></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    />
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
                        <?php foreach ($nav_menu as $menu): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($menu['url']); ?>" 
                               <?php echo $menu['active'] ? 'class="active"' : ''; ?>>
                                <?php echo htmlspecialchars($menu['text']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <li><a href="siswa_login.php" class="btn-login-siswa"><i class="fas fa-sign-in-alt"></i> Login Siswa</a></li>
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
                <h1>Kontak Kami</h1>
                <p>
                    Hubungi kami untuk informasi lebih lanjut tentang program bimbingan
                    belajar
                </p>
                <nav class="breadcrumb">
                    <ol>
                        <li><a href="index.php">Beranda</a></li>
                        <li class="current">Kontak</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <!-- Kontak Info Section -->
    <section class="contact-info-section">
        <div class="container">
            <div class="section-header">
                <h2>Informasi Kontak</h2>
                <p>Berbagai cara untuk menghubungi Bimbingan Belajar Peta Ilmu</p>
            </div>
            <div class="contact-info-grid">
                <?php foreach ($contact_info as $info): ?>
                <div class="contact-info-card">
                    <div class="contact-icon">
                        <i class="<?php echo htmlspecialchars($info['icon']); ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($info['title']); ?></h3>
                    <p><?php echo $info['content']; // Sudah aman karena dari data internal ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="section-header">
                <h2>Lokasi Kami</h2>
                <p>Temukan lokasi Bimbingan Belajar Peta Ilmu dengan mudah</p>
            </div>
        </div>
        <div class="map-container">
            <div class="map-wrapper">
                <!-- Google Maps Embed -->
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3988.716760568575!2d116.65701907485216!3d-1.3464030818094193!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMcKwMjAnNDcuMSJTIDExNsKwMzknMzAuMyJF!5e0!3m2!1sid!2sid!4v1748070033497!5m2!1sid!2sid"
                    width="100%"
                    height="450"
                    style="border: 0"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                >
                </iframe>
                <div class="map-overlay">
                    <div class="map-info">
                        <h3>Bimbingan Belajar Peta Ilmu</h3>
                        <p>
                            <i class="fas fa-map-marker-alt"></i> Perumahan Nuansa Petung
                            Blok C No.9
                        </p>
                        <a
                            href="https://maps.app.goo.gl/1g7c2cgPvYi5LDF99"
                            target="_blank"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-directions"></i> Petunjuk Arah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header">
                <h2>Pertanyaan yang Sering Diajukan</h2>
                <p>Jawaban untuk pertanyaan umum seputar layanan kami</p>
            </div>
            <div class="faq-container">
                <?php foreach ($faq_data as $index => $faq): ?>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?php echo htmlspecialchars($faq['answer']); ?></p>
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
                        <?php foreach ($footer_links as $link): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($link['url']); ?>">
                                <?php echo htmlspecialchars($link['text']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-program">
                    <h4>Program Kami</h4>
                    <ul>
                        <?php foreach ($footer_programs as $program): ?>
                        <li>
                            <a href="<?php echo htmlspecialchars($program['url']); ?>">
                                <?php echo htmlspecialchars($program['text']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Kontak Kami</h4>
                    <ul>
                        <?php foreach ($footer_contacts as $contact): ?>
                        <li>
                            <i class="<?php echo htmlspecialchars($contact['icon']); ?>"></i> 
                            <?php echo htmlspecialchars($contact['text']); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Bimbingan Belajar Peta Ilmu. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        // FAQ Accordion
        document.querySelectorAll(".faq-question").forEach((question) => {
            question.addEventListener("click", () => {
                const faqItem = question.parentElement;
                const isActive = faqItem.classList.contains("active");

                // Close all FAQ items
                document.querySelectorAll(".faq-item").forEach((item) => {
                    item.classList.remove("active");
                });

                // Open clicked item if it wasn't active
                if (!isActive) {
                    faqItem.classList.add("active");
                }
            });
        });
    </script>
</body>
</html>