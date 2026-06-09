-- ============================================================
--  DATABASE: peta_ilmu
--  Project : Bimbingan Belajar Peta Ilmu
--  Dibuat  : 2025
--  Deskripsi: File SQL lengkap untuk project PHP native Peta Ilmu.
--             Semua nama tabel dan kolom disesuaikan dengan query
--             yang ada di kode PHP (classes/*.php).
--
--  Cara import:
--    1. Buka phpMyAdmin  -> http://localhost/phpmyadmin
--    2. Buat database baru bernama  peta_ilmu  (jika belum ada)
--    3. Klik tab "Import", pilih file ini, klik "Go"
--
--  Admin login:
--    username : admin
--    password : admin123
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- Buat database (jalankan jika belum dibuat manual)
-- ============================================================
CREATE DATABASE IF NOT EXISTS `peta_ilmu`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `peta_ilmu`;

-- ============================================================
-- 1. TABEL: admins
--    Dipakai di: classes/AdminManager.php
--    Kolom   : id, username, password
-- ============================================================
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(100) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data awal admin
-- username: admin | password: admin123  (bcrypt hash)
INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$10$clQhyIbKBoQJ0VGQ.CMyB.Ce.EP4tnY/OzsBt.tU5p7f3rM3xZJz6');


-- ============================================================
-- 2. TABEL: programs
--    Dipakai di: classes/ProgramManager.php
--    Kolom   : id, program_code, category, icon, title,
--              description, duration, frequency, subjects (JSON)
-- ============================================================
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `program_code` VARCHAR(50)  NOT NULL UNIQUE,
  `category`     VARCHAR(20)  NOT NULL COMMENT 'sd | smp | sma',
  `icon`         VARCHAR(100) NOT NULL DEFAULT 'fas fa-book',
  `title`        VARCHAR(255) NOT NULL,
  `description`  TEXT         NOT NULL,
  `duration`     VARCHAR(100) NOT NULL,
  `frequency`    VARCHAR(100) NOT NULL,
  `subjects`     JSON                  COMMENT 'Array mata pelajaran dalam format JSON',
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data contoh program SD, SMP, SMA
INSERT INTO `programs` (`program_code`, `category`, `icon`, `title`, `description`, `duration`, `frequency`, `subjects`) VALUES
('PROG-SD-001',  'sd',  'fas fa-school',    'Program SD',  'Program pembelajaran lengkap untuk siswa SD kelas 1-6. Dirancang untuk membangun fondasi akademik yang kuat.',  '1-2 jam per sesi', '2-3 kali per minggu', '["Matematika","Bahasa Indonesia","Bahasa Inggris","IPA"]'),
('PROG-SMP-001', 'smp', 'fas fa-book-open', 'Program SMP', 'Program bimbingan intensif untuk siswa SMP kelas 7-9. Mempersiapkan siswa menghadapi ujian nasional dengan percaya diri.', '1-2 jam per sesi', '2-3 kali per minggu', '["Matematika","Bahasa Indonesia","Bahasa Inggris","IPA","IPS"]'),
('PROG-SMA-001', 'sma', 'fas fa-graduation-cap', 'Program SMA', 'Program persiapan intensif untuk siswa SMA kelas 10-12. Fokus pada penguasaan materi ujian dan persiapan SNBT.', '2 jam per sesi', '3 kali per minggu', '["Matematika","Fisika","Kimia","Biologi","Bahasa Inggris","Bahasa Indonesia"]');


-- ============================================================
-- 3. TABEL: program_features
--    Dipakai di: classes/ProgramManager.php
--    Kolom   : id, program_id, feature_text
-- ============================================================
DROP TABLE IF EXISTS `program_features`;
CREATE TABLE `program_features` (
  `id`           INT  NOT NULL AUTO_INCREMENT,
  `program_id`   INT  NOT NULL,
  `feature_text` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_features_program` (`program_id`),
  CONSTRAINT `fk_features_program`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fitur program SD
INSERT INTO `program_features` (`program_id`, `feature_text`) VALUES
(1, 'Matematika - konsep dasar hingga operasi hitung lanjutan'),
(1, 'Bahasa Indonesia - membaca, menulis, dan berbicara'),
(1, 'Bahasa Inggris - kosakata dan percakapan dasar'),
(1, 'IPA - mengenal alam dan lingkungan sekitar'),
-- Fitur program SMP
(2, 'Matematika - aljabar, geometri, dan statistika'),
(2, 'Bahasa Indonesia - analisis teks dan karya sastra'),
(2, 'Bahasa Inggris - grammar dan kemampuan komunikasi'),
(2, 'IPA - fisika, kimia, dan biologi tingkat dasar'),
(2, 'IPS - sejarah, geografi, ekonomi, dan sosiologi'),
-- Fitur program SMA
(3, 'Matematika - kalkulus, trigonometri, dan statistika lanjutan'),
(3, 'Fisika - mekanika, termodinamika, dan listrik magnet'),
(3, 'Kimia - stoikiometri, kimia organik, dan larutan'),
(3, 'Biologi - sel, genetika, dan ekologi'),
(3, 'Persiapan SNBT dan UTBK intensif');


-- ============================================================
-- 4. TABEL: program_packages
--    Dipakai di: classes/ProgramManager.php & RegistrationManager.php
--    Kolom   : id, program_id, package_type, description,
--              package_icon, info, extra_info
-- ============================================================
DROP TABLE IF EXISTS `program_packages`;
CREATE TABLE `program_packages` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `program_id`   INT          NOT NULL,
  `package_type` VARCHAR(100) NOT NULL,
  `description`  VARCHAR(255) NOT NULL,
  `package_icon` VARCHAR(100) NOT NULL DEFAULT 'fas fa-box',
  `info`         TEXT,
  `extra_info`   TEXT,
  PRIMARY KEY (`id`),
  KEY `fk_packages_program` (`program_id`),
  CONSTRAINT `fk_packages_program`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Paket untuk Program SD (program_id = 1)
INSERT INTO `program_packages` (`program_id`, `package_type`, `description`, `package_icon`, `info`, `extra_info`) VALUES
(1, 'Kelas Reguler',                           'Max 5 Siswa : 1 Guru',  'fas fa-users',  'Belajar bersama dalam kelompok kecil', ''),
(1, 'Kelas Private - Petung/Girimukti',        '1 Siswa : 1 Guru',      'fas fa-home',   'Belajar privat di lokasi Petung/Girimukti', ''),
(1, 'Kelas Private - Luar Petung/Girimukti',   '1 Siswa : 1 Guru',      'fas fa-car',    'Belajar privat + biaya transportasi', 'Termasuk biaya transportasi pengajar'),
-- Paket untuk Program SMP (program_id = 2)
(2, 'Kelas Reguler',                           'Max 5 Siswa : 1 Guru',  'fas fa-users',  'Belajar bersama dalam kelompok kecil', ''),
(2, 'Kelas Private - Petung/Girimukti',        '1 Siswa : 1 Guru',      'fas fa-home',   'Belajar privat di lokasi Petung/Girimukti', ''),
(2, 'Kelas Private - Luar Petung/Girimukti',   '1 Siswa : 1 Guru',      'fas fa-car',    'Belajar privat + biaya transportasi', 'Termasuk biaya transportasi pengajar'),
-- Paket untuk Program SMA (program_id = 3)
(3, 'Kelas Reguler',                           'Max 5 Siswa : 1 Guru',  'fas fa-users',  'Belajar bersama dalam kelompok kecil', ''),
(3, 'Kelas Private - Petung/Girimukti',        '1 Siswa : 1 Guru',      'fas fa-home',   'Belajar privat di lokasi Petung/Girimukti', ''),
(3, 'Kelas Private - Luar Petung/Girimukti',   '1 Siswa : 1 Guru',      'fas fa-car',    'Belajar privat + biaya transportasi', 'Termasuk biaya transportasi pengajar');


-- ============================================================
-- 5. TABEL: package_prices
--    Dipakai di: classes/ProgramManager.php & RegistrationManager.php
--    Kolom   : id, package_id, price_label, price
-- ============================================================
DROP TABLE IF EXISTS `package_prices`;
CREATE TABLE `package_prices` (
  `id`          INT            NOT NULL AUTO_INCREMENT,
  `package_id`  INT            NOT NULL,
  `price_label` VARCHAR(50)    NOT NULL COMMENT '8x Pertemuan | 12x Pertemuan | Harian',
  `price`       DECIMAL(12, 2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_prices_package` (`package_id`),
  CONSTRAINT `fk_prices_package`
    FOREIGN KEY (`package_id`) REFERENCES `program_packages` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Harga paket SD
-- SD Reguler (package_id=1)
INSERT INTO `package_prices` (`package_id`, `price_label`, `price`) VALUES
(1, '8x Pertemuan',  160000),
(1, '12x Pertemuan', 240000),
(1, 'Harian',         20000),
-- SD Private Petung/Girimukti (package_id=2)
(2, '8x Pertemuan',  200000),
(2, '12x Pertemuan', 300000),
(2, 'Harian',         25000),
-- SD Private Luar Petung/Girimukti (package_id=3)
(3, '8x Pertemuan',  200000),
(3, '12x Pertemuan', 300000),
(3, 'Harian',         25000),
-- SMP Reguler (package_id=4)
(4, '8x Pertemuan',  160000),
(4, '12x Pertemuan', 240000),
(4, 'Harian',         20000),
-- SMP Private Petung/Girimukti (package_id=5)
(5, '8x Pertemuan',  200000),
(5, '12x Pertemuan', 300000),
(5, 'Harian',         25000),
-- SMP Private Luar Petung/Girimukti (package_id=6)
(6, '8x Pertemuan',  200000),
(6, '12x Pertemuan', 300000),
(6, 'Harian',         25000),
-- SMA Reguler (package_id=7)
(7, '8x Pertemuan',  160000),
(7, '12x Pertemuan', 240000),
(7, 'Harian',         20000),
-- SMA Private Petung/Girimukti (package_id=8)
(8, '8x Pertemuan',  200000),
(8, '12x Pertemuan', 300000),
(8, 'Harian',         25000),
-- SMA Private Luar Petung/Girimukti (package_id=9)
(9, '8x Pertemuan',  200000),
(9, '12x Pertemuan', 300000),
(9, 'Harian',         25000);


-- ============================================================
-- 6. TABEL: program_benefits
--    Dipakai di: classes/ProgramManager.php -> getProgramBenefits()
--    Kolom   : id, icon, title, description
-- ============================================================
DROP TABLE IF EXISTS `program_benefits`;
CREATE TABLE `program_benefits` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `icon`        VARCHAR(100) NOT NULL DEFAULT 'fas fa-star',
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT         NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `program_benefits` (`icon`, `title`, `description`) VALUES
('fas fa-chalkboard-teacher', 'Pengajar Berpengalaman',  'Semua pengajar kami berpengalaman dan berdedikasi tinggi dalam mendidik siswa.'),
('fas fa-users',              'Kelas Kecil',             'Maksimal 5 siswa per kelas agar setiap siswa mendapatkan perhatian optimal.'),
('fas fa-chart-line',         'Metode Terstruktur',      'Kurikulum terstruktur mengikuti silabus sekolah yang disempurnakan secara berkala.'),
('fas fa-clock',              'Jadwal Fleksibel',        'Jadwal belajar fleksibel yang dapat disesuaikan dengan aktivitas siswa.'),
('fas fa-award',              'Jaminan Kualitas',        'Kami berkomitmen memberikan kualitas pendidikan terbaik untuk setiap siswa.'),
('fas fa-home',               'Layanan Privat',          'Tersedia layanan bimbingan di rumah dengan pengajar yang datang langsung.');


-- ============================================================
-- 7. TABEL: program_faqs
--    Dipakai di: classes/ProgramManager.php -> getProgramFAQs()
--    Kolom   : id, question, answer
-- ============================================================
DROP TABLE IF EXISTS `program_faqs`;
CREATE TABLE `program_faqs` (
  `id`       INT  NOT NULL AUTO_INCREMENT,
  `question` TEXT NOT NULL,
  `answer`   TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `program_faqs` (`question`, `answer`) VALUES
('Bagaimana cara mendaftar di Peta Ilmu?',          'Anda dapat mendaftar secara online melalui halaman pendaftaran di website kami, datang langsung ke kantor kami, atau menghubungi kami via telepon/WhatsApp.'),
('Berapa jumlah siswa per kelas?',                  'Kami membatasi jumlah siswa maksimal 5 orang per kelas untuk memastikan setiap siswa mendapat perhatian yang optimal dari pengajar.'),
('Apakah tersedia kelas privat di rumah?',          'Ya, tersedia paket Kelas Private dimana pengajar akan datang langsung ke rumah siswa. Tersedia untuk wilayah Petung/Girimukti maupun luar wilayah tersebut.'),
('Apakah ada konsultasi dengan orang tua?',         'Ya, kami mengadakan sesi konsultasi rutin dengan orang tua setiap bulan untuk membahas perkembangan belajar anak.'),
('Apa saja mata pelajaran yang tersedia?',          'Tersedia Matematika, Bahasa Indonesia, Bahasa Inggris, IPA, IPS, Fisika, Kimia, Biologi, dan mata pelajaran lainnya sesuai jenjang pendidikan.');


-- ============================================================
-- 8. TABEL: pendaftaran
--    Dipakai di: classes/RegistrationManager.php
--    Kolom   : semua field formulir pendaftaran
-- ============================================================
DROP TABLE IF EXISTS `pendaftaran`;
CREATE TABLE `pendaftaran` (
  `id`            INT            NOT NULL AUTO_INCREMENT,
  `student_id`    INT                     DEFAULT NULL,
  `nama_lengkap`  VARCHAR(255)   NOT NULL,
  `tanggal_lahir` DATE           NOT NULL,
  `jenis_kelamin` ENUM('L','P')  NOT NULL,
  `alamat`        TEXT           NOT NULL,
  `telepon`       VARCHAR(20)    NOT NULL,
  `email`         VARCHAR(255)            DEFAULT NULL,
  `jenjang`       VARCHAR(10)    NOT NULL COMMENT 'sd | smp | sma',
  `kelas`         VARCHAR(20)    NOT NULL,
  `sekolah`       VARCHAR(255)   NOT NULL,
  `package_id`    INT            NOT NULL,
  `package_type`  VARCHAR(100)   NOT NULL,
  `durasi`        VARCHAR(50)    NOT NULL COMMENT '8x Pertemuan | 12x Pertemuan | Harian',
  `jumlah_hari`   INT                     DEFAULT NULL COMMENT 'Diisi hanya jika durasi = harian',
  `nama_ortu`     VARCHAR(255)   NOT NULL,
  `pekerjaan_ortu` VARCHAR(255)           DEFAULT NULL,
  `telepon_ortu`  VARCHAR(20)    NOT NULL,
  `motivasi`      TEXT                    DEFAULT NULL,
  `referensi`     VARCHAR(100)            DEFAULT NULL,
  `total_price`   DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `status`        ENUM('pending','confirmed','rejected') NOT NULL DEFAULT 'pending',
  `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pendaftaran_package` (`package_id`),
  KEY `fk_pendaftaran_student` (`student_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_pendaftaran_package`
    FOREIGN KEY (`package_id`) REFERENCES `program_packages` (`id`)
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pendaftaran_student`
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 9. TABEL: registration_subjects
--    Dipakai di: classes/RegistrationManager.php
--    Kolom   : id, registration_id, subject_name
-- ============================================================
DROP TABLE IF EXISTS `registration_subjects`;
CREATE TABLE `registration_subjects` (
  `id`              INT          NOT NULL AUTO_INCREMENT,
  `registration_id` INT          NOT NULL,
  `subject_name`    VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_subjects_registration` (`registration_id`),
  CONSTRAINT `fk_subjects_registration`
    FOREIGN KEY (`registration_id`) REFERENCES `pendaftaran` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 10. TABEL: organisasi_info
--     Dipakai di: classes/ProfilManager.php -> getOrganisasiInfo()
--     Kolom   : id, visi, tahun_berdiri, jumlah_siswa_awal
-- ============================================================
DROP TABLE IF EXISTS `organisasi_info`;
CREATE TABLE `organisasi_info` (
  `id`                INT          NOT NULL AUTO_INCREMENT,
  `visi`              TEXT         NOT NULL,
  `tahun_berdiri`     YEAR         NOT NULL,
  `jumlah_siswa_awal` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `organisasi_info` (`visi`, `tahun_berdiri`, `jumlah_siswa_awal`) VALUES
('Menjadi lembaga bimbingan belajar terpercaya yang mencetak generasi unggul, berkarakter, dan berprestasi di Penajam Paser Utara.', 2020, 10);


-- ============================================================
-- 11. TABEL: sejarah_organisasi
--     Dipakai di: classes/ProfilManager.php -> getSejarahOrganisasi()
--     Kolom   : id, paragraf, urutan
-- ============================================================
DROP TABLE IF EXISTS `sejarah_organisasi`;
CREATE TABLE `sejarah_organisasi` (
  `id`      INT  NOT NULL AUTO_INCREMENT,
  `paragraf` TEXT NOT NULL,
  `urutan`  INT  NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sejarah_organisasi` (`paragraf`, `urutan`) VALUES
('Bimbingan Belajar Peta Ilmu didirikan pada tahun 2020 di Penajam Paser Utara, Kalimantan Timur. Berawal dari semangat untuk meningkatkan kualitas pendidikan di daerah, lembaga ini hadir dengan misi memberikan layanan bimbingan belajar yang berkualitas dan terjangkau.', 1),
('Dengan memulai perjalanan bersama 10 siswa pertama, Peta Ilmu terus berkembang dan dipercaya oleh ratusan keluarga di Penajam Paser Utara. Kami menghadirkan pengajar-pengajar berpengalaman dan berkomitmen untuk mendampingi setiap siswa meraih prestasi terbaik.', 2),
('Hingga saat ini, Peta Ilmu telah membantu ratusan siswa dari berbagai jenjang pendidikan - SD, SMP, hingga SMA - untuk meningkatkan pemahaman akademik dan meraih tujuan belajar mereka.', 3);


-- ============================================================
-- 12. TABEL: misi_organisasi
--     Dipakai di: classes/ProfilManager.php -> getMisiOrganisasi()
--     Kolom   : id, misi_text, urutan
-- ============================================================
DROP TABLE IF EXISTS `misi_organisasi`;
CREATE TABLE `misi_organisasi` (
  `id`        INT  NOT NULL AUTO_INCREMENT,
  `misi_text` TEXT NOT NULL,
  `urutan`    INT  NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `misi_organisasi` (`misi_text`, `urutan`) VALUES
('Memberikan layanan bimbingan belajar berkualitas tinggi yang dapat diakses oleh semua kalangan masyarakat.', 1),
('Mengembangkan potensi akademik dan karakter siswa secara menyeluruh melalui pendekatan pembelajaran yang inovatif.', 2),
('Menyediakan tenaga pengajar yang kompeten, berdedikasi, dan terus meningkatkan kompetensi profesionalnya.', 3),
('Menjalin kemitraan yang erat dengan orang tua dan sekolah untuk mendukung perkembangan belajar setiap siswa.', 4),
('Berkontribusi aktif dalam peningkatan kualitas pendidikan di Penajam Paser Utara dan sekitarnya.', 5);


-- ============================================================
-- 13. TABEL: nilai_organisasi
--     Dipakai di: classes/ProfilManager.php -> getNilaiOrganisasi()
--     Kolom   : id, icon, nama, deskripsi
-- ============================================================
DROP TABLE IF EXISTS `nilai_organisasi`;
CREATE TABLE `nilai_organisasi` (
  `id`       INT          NOT NULL AUTO_INCREMENT,
  `icon`     VARCHAR(100) NOT NULL DEFAULT 'fas fa-star',
  `nama`     VARCHAR(100) NOT NULL,
  `deskripsi` TEXT        NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nilai_organisasi` (`icon`, `nama`, `deskripsi`) VALUES
('fas fa-heart',          'Integritas',    'Kami menjunjung tinggi kejujuran dan transparansi dalam setiap aspek layanan kami.'),
('fas fa-lightbulb',      'Inovasi',       'Kami terus berinovasi dalam metode pembelajaran untuk hasil terbaik bagi siswa.'),
('fas fa-handshake',      'Kepercayaan',   'Kepercayaan orang tua dan siswa adalah fondasi utama layanan kami.'),
('fas fa-award',          'Keunggulan',    'Kami berkomitmen untuk selalu memberikan layanan pendidikan di atas standar.'),
('fas fa-users',          'Kebersamaan',   'Kami percaya bahwa belajar bersama menciptakan lingkungan yang lebih baik dan menyenangkan.');


-- ============================================================
-- 14. TABEL: struktur_organisasi
--     Dipakai di: classes/ProfilManager.php -> getStrukturOrganisasi()
--     Kolom   : id, level, nama, posisi, deskripsi, foto
-- ============================================================
DROP TABLE IF EXISTS `struktur_organisasi`;
CREATE TABLE `struktur_organisasi` (
  `id`       INT          NOT NULL AUTO_INCREMENT,
  `level`    INT          NOT NULL DEFAULT 1 COMMENT '1=pimpinan, 2=manajer, 3=staf',
  `nama`     VARCHAR(255) NOT NULL,
  `posisi`   VARCHAR(255) NOT NULL,
  `deskripsi` TEXT,
  `foto`     VARCHAR(255)          DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `struktur_organisasi` (`level`, `nama`, `posisi`, `deskripsi`, `foto`) VALUES
(1, 'Pimpinan Peta Ilmu', 'Ketua / Pendiri',        'Bertanggung jawab atas visi, misi, dan arah pengembangan lembaga secara keseluruhan.', NULL),
(2, 'Koordinator Akademik', 'Koordinator Akademik', 'Mengelola kurikulum, jadwal pengajaran, dan kualitas pembelajaran seluruh program.', NULL),
(3, 'Staf Administrasi',  'Administrasi & Keuangan','Menangani administrasi, keuangan, dan hubungan dengan orang tua siswa.', NULL);


-- ============================================================
-- 15. TABEL: mata_pelajaran
--     Dipakai di: classes/ProfilManager.php -> getMataPelajaranFilter()
--                                           -> getTimPengajar() (JOIN)
--     Kolom   : id, kode, nama
-- ============================================================
DROP TABLE IF EXISTS `mata_pelajaran`;
CREATE TABLE `mata_pelajaran` (
  `id`   INT          NOT NULL AUTO_INCREMENT,
  `kode` VARCHAR(20)  NOT NULL UNIQUE,
  `nama` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `mata_pelajaran` (`kode`, `nama`) VALUES
('mtk',  'Matematika'),
('bind', 'Bahasa Indonesia'),
('bing', 'Bahasa Inggris'),
('ipa',  'IPA'),
('ips',  'IPS'),
('fis',  'Fisika'),
('kim',  'Kimia'),
('bio',  'Biologi');


-- ============================================================
-- 16. TABEL: tim_pengajar
--     Dipakai di: classes/ProfilManager.php -> getTimPengajar()
--     Kolom   : id, nama, mata_pelajaran_id, deskripsi, foto, status
-- ============================================================
DROP TABLE IF EXISTS `tim_pengajar`;
CREATE TABLE `tim_pengajar` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `nama`             VARCHAR(255) NOT NULL,
  `mata_pelajaran_id` INT         NOT NULL,
  `deskripsi`        TEXT,
  `foto`             VARCHAR(255)          DEFAULT NULL,
  `status`           ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`),
  KEY `fk_pengajar_mapel` (`mata_pelajaran_id`),
  CONSTRAINT `fk_pengajar_mapel`
    FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tim_pengajar` (`nama`, `mata_pelajaran_id`, `deskripsi`, `foto`, `status`) VALUES
('Pengajar Matematika',      1, 'Berpengalaman mengajar matematika untuk semua jenjang SD hingga SMA.', NULL, 'aktif'),
('Pengajar Bahasa Indonesia', 2, 'Ahli dalam pembelajaran bahasa Indonesia termasuk sastra dan menulis kreatif.', NULL, 'aktif'),
('Pengajar Bahasa Inggris',  3, 'Fasih dalam bahasa Inggris dan berpengalaman mengajarkan grammar serta percakapan.', NULL, 'aktif'),
('Pengajar IPA',             4, 'Berpengalaman mengajarkan IPA dari tingkat dasar hingga menengah.', NULL, 'aktif'),
('Pengajar Fisika',          6, 'Spesialis fisika untuk SMA dengan pengalaman persiapan SNBT.', NULL, 'aktif'),
('Pengajar Kimia',           7, 'Ahli kimia organik dan anorganik untuk jenjang SMA.', NULL, 'aktif');


-- ============================================================
-- 17. TABEL: kontak_info
--     Dipakai di: classes/ProfilManager.php -> getKontakInfo()
--     Kolom   : id, jenis, nilai, status, urutan
-- ============================================================
DROP TABLE IF EXISTS `kontak_info`;
CREATE TABLE `kontak_info` (
  `id`     INT          NOT NULL AUTO_INCREMENT,
  `jenis`  VARCHAR(20)  NOT NULL COMMENT 'alamat | telepon | email | fax',
  `nilai`  VARCHAR(255) NOT NULL,
  `status` ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `urutan` INT          NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_jenis_status` (`jenis`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kontak_info` (`jenis`, `nilai`, `status`, `urutan`) VALUES
('alamat',  'Perum Nuansa Blok.C No.9, Kelurahan Petung, Penajam Paser Utara 76143, Kalimantan Timur', 'aktif', 1),
('alamat',  'Girimukti Strat 3, Desa Girimukti, Penajam Paser Utara 76143, Kalimantan Timur',          'aktif', 2),
('telepon', '+62 898-1792-917',  'aktif', 1),
('telepon', '+62 822-5513-1993', 'aktif', 2);

-- Tabel siswa untuk fitur login siswa
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    jenjang VARCHAR(20) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data akun siswa contoh
-- username: siswa | password: siswa123 (bcrypt hash)
INSERT INTO students (username, nama, email, password, jenjang, kelas) VALUES
('siswa', 'Siswa Contoh', 'siswa@example.com', '$2y$12$HzlWuZeNo.dE7Uaed7PG4u5xjjJSfdPZZSuEMzVbvVjuaCamz9CPa', 'sma', '12');

-- ============================================================
-- 19. TABEL: learning_data
--     Dipakai untuk fitur input data belajar, perkembangan belajar,
--     dan rekomendasi layanan pembelajaran.
-- ============================================================
CREATE TABLE IF NOT EXISTS `learning_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `mata_pelajaran` VARCHAR(100) NOT NULL,
  `nilai` INT NOT NULL,
  `tingkat_kesulitan` VARCHAR(20) NOT NULL,
  `gaya_belajar` VARCHAR(50) DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `tanggal_input` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_learning_student` (`student_id`),
  CONSTRAINT `fk_learning_student`
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
