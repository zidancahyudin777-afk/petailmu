<?php
// Konfigurasi halaman
$page_title = 'Bimbingan Belajar Peta Ilmu - Beranda';
$current_page = 'index';
$company_name = 'Peta Ilmu';
$current_year = date('Y');

// Data Program
$programs = [
    [
        'id' => 'sd',
        'name' => 'Program SD',
        'image' => 'programsd.png',
        'description' => 'Penguatan konsep dasar dengan metode belajar yang menyenangkan'
    ],
    [
        'id' => 'smp',
        'name' => 'Program SMP',
        'image' => 'programsmp.png',
        'description' => 'Meningkatkan kompetensi akademik dan persiapan menghadapi ujian nasional'
    ],
    [
        'id' => 'sma',
        'name' => 'Program SMA',
        'image' => 'programsma.png',
        'description' => 'Persiapan ujian dan strategi masuk perguruan tinggi favorit'
    ]
];

// Data Hero Slider
$hero_slides = [
    [
        'title' => 'Raih Prestasi Terbaikmu Bersama Peta Ilmu',
        'subtitle' => 'Solusi belajar terpadu dengan metode pembelajaran yang inovatif dan efektif.',
        'button_text' => 'Daftar Sekarang',
        'button_link' => 'pendaftaran.php',
        'image' => 'images/hero-1.jpg'
    ],
    [
        'title' => 'Tenaga Pengajar Muda',
        'subtitle' => 'Dibimbing oleh pengajar muda dan berkualitas yang akan menuntun keberhasilan belajarmu.',
        'button_text' => 'Lihat Pengajar',
        'button_link' => 'profil.php#pengajar',
        'image' => 'images/hero-2.jpg'
    ],
    [
        'title' => 'Program Belajar untuk Semua Jenjang',
        'subtitle' => 'Tersedia berbagai program untuk SD, SMP, SMA.',
        'button_text' => 'Lihat Program',
        'button_link' => 'program.php',
        'image' => 'images/hero-3.jpg'
    ]
];

// Data Keunggulan
$keunggulan = [
    [
        'icon' => 'fas fa-user-graduate',
        'title' => 'Pengajar Generasi Muda',
        'description' => 'Tenaga pengajar muda dan mengikuti perkembangan zaman'
    ],
    [
        'icon' => 'fas fa-book',
        'title' => 'Kurikulum Terintegrasi',
        'description' => 'Materi pembelajaran yang disesuaikan dengan kurikulum nasional'
    ],
    [
        'icon' => 'fas fa-chart-line',
        'title' => 'Pemantauan Kemajuan',
        'description' => 'Evaluasi berkala dan laporan perkembangan belajar siswa'
    ],
    [
        'icon' => 'fas fa-users',
        'title' => 'Kelas Kecil',
        'description' => 'Perhatian lebih intensif dengan jumlah siswa terbatas per kelas'
    ]
];

// Data Testimoni
$testimonials = [
    [
        'name' => 'Aisyah Aqila',
        'role' => 'Siswi SMP Kelas 7',
        'rating' => 5,
        'text' => 'Berkat bimbingan di Peta Ilmu, pemahaman materi ipa saya meningkat drastis. Metode pengajaran yang mudah dipahami membuat saya lebih percaya diri menghadapi ujian.'
    ],
    [
        'name' => 'Fandi',
        'role' => 'Orang Tua Siswa',
        'rating' => 4.5,
        'text' => 'Sebagai orang tua, saya sangat mengapresiasi laporan perkembangan belajar yang diberikan Peta Ilmu secara rutin. Anak saya juga menjadi lebih semangat belajar sejak bergabung.'
    ],
    [
        'name' => 'Zaqia',
        'role' => 'Siswi SD',
        'rating' => 5,
        'text' => 'Yang paling aku suka, kakak guru ngajarinnya seru. Jadi pas lagi belajar, aku nggak gampang bosen deh.'
    ]
];

// Data Kontak
$contacts = [
    [
        'icon' => 'fas fa-map-marker-alt',
        'text' => 'Perumahan Nuansa Petung Blok C No.9'
    ],
    [
        'icon' => 'fas fa-map-marker-alt',
        'text' => 'Girimukti Rt.10 No.55 Strat 3'
    ],
    [
        'icon' => 'fas fa-phone',
        'text' => '+62 8981792917'
    ],
    [
        'icon' => 'fas fa-phone',
        'text' => '+62 82255131993'
    ]
];

// Footer Links
$footer_links = [
    ['name' => 'Beranda', 'link' => 'index.php'],
    ['name' => 'Profil', 'link' => 'profil.php'],
    ['name' => 'Program', 'link' => 'program.php'],
    ['name' => 'Pendaftaran', 'link' => 'pendaftaran.php'],
    ['name' => 'Login Siswa', 'link' => 'siswa_login.php'],
    ['name' => 'Kontak', 'link' => 'kontak.php']
];

// Program Footer Links
$program_footer_links = [
    ['name' => 'Program SD', 'link' => 'program.php#sd'],
    ['name' => 'Program SMP', 'link' => 'program.php#smp'],
    ['name' => 'Program SMA', 'link' => 'program.php#sma']
];
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="styleindex.css" />
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
              <li><a href="index.php" class="active">Beranda</a></li>
              <li><a href="profil.php">Profil</a></li>
              <li><a href="program.php">Program</a></li>
              <li><a href="pendaftaran.php">Pendaftaran</a></li>
              <li><a href="kontak.php">Kontak</a></li>
              <li><a href="siswa_login.php" class="btn-login-siswa">Login Siswa</a></li>
            </ul>
          </nav>
          <div class="mobile-menu">
            <i class="fas fa-bars"></i>
          </div>
        </div>
      </div>
    </header>

     <section class="hero-section">
  <div class="hero-slider">
    <?php foreach($hero_slides as $index => $slide): ?>
    <div class="slide <?php echo ($index == 0) ? 'active' : ''; ?>">
      <div class="container">
        <div class="hero-content">
          <h1><?php echo $slide['title']; ?></h1>
          <p><?php echo $slide['subtitle']; ?></p>
          <a href="<?php echo $slide['button_link']; ?>" 
             class="btn btn-primary"
             onclick="simulateNavigation('<?php echo $slide['button_link']; ?>')">
            <?php echo $slide['button_text']; ?>
          </a>
        </div>
      </div>
      <div class="hero-image" 
           style="background-image: url('<?php echo $slide['image']; ?>')">
      </div>
    </div>
    <?php endforeach; ?>
    
    <div class="slider-controls">
      <div class="prev-btn"><i class="fas fa-chevron-left"></i></div>
      <div class="next-btn"><i class="fas fa-chevron-right"></i></div>
    </div>
    <div class="slider-indicators">
      <?php foreach($hero_slides as $index => $slide): ?>
      <span class="indicator <?php echo ($index == 0) ? 'active' : ''; ?>"></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

    <!-- Program Highlight Section -->
    <section class="program-highlight">
      <div class="container">
        <div class="section-header">
          <h2>Program Unggulan</h2>
          <p>
            Program pembelajaran terbaik yang dirancang khusus untuk
            keberhasilan siswa
          </p>
        </div>
        <div class="program-cards">
          <?php foreach($programs as $program): ?>
          <div class="program-card">
            <div class="card-image">
              <img src="images/<?php echo $program['image']; ?>" alt="<?php echo $program['name']; ?>" />
            </div>
            <div class="card-content">
              <h3><?php echo $program['name']; ?></h3>
              <p><?php echo $program['description']; ?></p>
              <a href="program.php#<?php echo $program['id']; ?>" class="btn btn-secondary">Selengkapnya</a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="program-cta">
          <a href="program.php" class="btn btn-outline">Lihat Semua Program</a>
        </div>
      </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="keunggulan-section">
      <div class="container">
        <div class="section-header">
          <h2>Mengapa Memilih <?php echo $company_name; ?>?</h2>
          <p>Keunggulan kami dalam membantu siswa meraih prestasi terbaik</p>
        </div>
        <div class="keunggulan-items">
          <?php foreach($keunggulan as $item): ?>
          <div class="keunggulan-item">
            <div class="icon">
              <i class="<?php echo $item['icon']; ?>"></i>
            </div>
            <h3><?php echo $item['title']; ?></h3>
            <p><?php echo $item['description']; ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonial-section">
      <div class="container">
        <div class="section-header">
          <h2>Testimoni Siswa & Orang Tua</h2>
          <p>Pendapat mereka tentang program bimbingan belajar di <?php echo $company_name; ?></p>
        </div>
        <div class="testimonial-slider">
          <?php foreach($testimonials as $index => $testimonial): ?>
          <div class="testimonial-slide <?php echo ($index == 0) ? 'active' : ''; ?>">
            <div class="testimonial-card">
              <div class="testimonial-content">
                <div class="rating">
                  <?php 
                  $full_stars = floor($testimonial['rating']);
                  $half_star = ($testimonial['rating'] - $full_stars) >= 0.5;
                  
                  for($i = 1; $i <= 5; $i++):
                    if($i <= $full_stars): ?>
                      <i class="fas fa-star"></i>
                    <?php elseif($i == $full_stars + 1 && $half_star): ?>
                      <i class="fas fa-star-half-alt"></i>
                    <?php else: ?>
                      <i class="far fa-star"></i>
                    <?php endif;
                  endfor; ?>
                </div>
                <p>"<?php echo $testimonial['text']; ?>"</p>
                <div class="testimonial-author">
                  <h4><?php echo $testimonial['name']; ?></h4>
                  <span><?php echo $testimonial['role']; ?></span>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <div class="testimonial-controls">
            <div class="testimonial-prev">
              <i class="fas fa-chevron-left"></i>
            </div>
            <div class="testimonial-next">
              <i class="fas fa-chevron-right"></i>
            </div>
          </div>
          <div class="testimonial-indicators">
            <?php foreach($testimonials as $index => $testimonial): ?>
            <span class="testimonial-indicator <?php echo ($index == 0) ? 'active' : ''; ?>"></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Mulai Perjalanan Belajarmu Sekarang!</h2>
          <p>
            Daftarkan diri atau anakmu untuk mengikuti program pembelajaran di
            <?php echo $company_name; ?> dan raih prestasi terbaik.
          </p>
          <div class="cta-buttons">
            <a href="pendaftaran.php" class="btn btn-white">Daftar Sekarang</a>
            <a href="kontak.php" class="btn-ghost-white">Hubungi Kami</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-logo">
            <img src="images/IMG_3899.PNG" alt="Logo <?php echo $company_name; ?>" />
            <h3>Bimbingan Belajar <?php echo $company_name; ?></h3>
            <p>Di setiap tempat, di situ ilmu didapat!</p>
          </div>
          <div class="footer-links">
            <h4>Link Cepat</h4>
            <ul>
              <?php foreach($footer_links as $link): ?>
              <li><a href="<?php echo $link['link']; ?>"><?php echo $link['name']; ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="footer-program">
            <h4>Program Kami</h4>
            <ul>
              <?php foreach($program_footer_links as $link): ?>
              <li><a href="<?php echo $link['link']; ?>"><?php echo $link['name']; ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="footer-contact">
            <h4>Kontak Kami</h4>
            <ul>
              <?php foreach($contacts as $contact): ?>
              <li>
                <i class="<?php echo $contact['icon']; ?>"></i> <?php echo $contact['text']; ?>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
        <p>&copy; <?php echo $current_year; ?> Bimbingan Belajar <?php echo $company_name; ?>. Hak Cipta Dilindungi.</p>
        </div>
      </div>
    </footer>
  </body>
</html>