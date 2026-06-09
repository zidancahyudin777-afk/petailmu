<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header('Location: siswa_login.php');
    exit;
}

require_once __DIR__ . '/classes/StudentManager.php';
require_once __DIR__ . '/config/database.php';

$studentManager = new StudentManager();
$student = $studentManager->getStudentById($_SESSION['student_id']);

if (!$student) {
    header('Location: siswa_logout.php');
    exit;
}

$database = new Database();
$pdo = $database->getConnection();
$student_id = $_SESSION['student_id'];

$query = "SELECT * FROM learning_data 
          WHERE student_id = :student_id 
          ORDER BY tanggal_input DESC 
          LIMIT 1";

$stmt = $pdo->prepare($query);
$stmt->execute([':student_id' => $student_id]);
$data = $stmt->fetch();

function jenjangLabel($j) {
    return match(strtolower($j)) {
        'sd'  => 'SD/MI',
        'smp' => 'SMP/MTs',
        'sma' => 'SMA/MA/SMK',
        default => strtoupper($j),
    };
}

function subjectFocus($subject) {
    $s = strtolower($subject ?? '');
    if (str_contains($s, 'matematika')) return 'pemahaman rumus, latihan bertahap, dan pembahasan soal cerita';
    if (str_contains($s, 'inggris')) return 'kosakata, grammar dasar, reading, dan latihan percakapan sederhana';
    if (str_contains($s, 'indonesia')) return 'pemahaman bacaan, penulisan jawaban, dan analisis teks';
    if (str_contains($s, 'ipa')) return 'konsep materi, hubungan sebab-akibat, dan latihan soal penerapan';
    if (str_contains($s, 'ips')) return 'pemahaman konsep, hafalan terarah, dan latihan studi kasus';
    return 'pemahaman konsep dasar, latihan soal, dan pembahasan materi yang belum dikuasai';
}

function styleStrategy($style) {
    $g = strtolower($style ?? '');
    if ($g === 'visual') {
        return [
            'title' => 'Strategi untuk gaya belajar visual',
            'items' => [
                'Gunakan rangkuman berwarna, bagan, tabel kecil, dan peta konsep.',
                'Minta tutor menjelaskan materi melalui contoh gambar atau alur langkah.',
                'Setelah belajar, buat catatan satu halaman berisi inti materi dan contoh soal.'
            ]
        ];
    }
    if ($g === 'auditori') {
        return [
            'title' => 'Strategi untuk gaya belajar auditori',
            'items' => [
                'Belajar dengan penjelasan lisan, diskusi, dan tanya jawab bersama tutor.',
                'Ulangi materi dengan cara menjelaskan kembali menggunakan kata-kata sendiri.',
                'Gunakan rekaman suara singkat atau pembahasan lisan untuk mengingat konsep.'
            ]
        ];
    }
    if ($g === 'kinestetik') {
        return [
            'title' => 'Strategi untuk gaya belajar kinestetik',
            'items' => [
                'Belajar langsung lewat latihan soal, praktik, simulasi, atau contoh kasus.',
                'Pecah materi menjadi langkah-langkah kecil lalu kerjakan satu per satu.',
                'Gunakan metode belajar aktif, misalnya menulis ulang rumus dan mencoba variasi soal.'
            ]
        ];
    }
    return [
        'title' => 'Strategi belajar yang disarankan',
        'items' => [
            'Mulai dari konsep dasar, lanjut ke latihan soal mudah, lalu naik ke soal sedang.',
            'Catat bagian yang masih membingungkan untuk dibahas dengan tutor.',
            'Lakukan evaluasi singkat setelah belajar agar perkembangan lebih mudah dipantau.'
        ]
    ];
}

function determineRecommendation($data) {
    if (!$data) return null;

    $nilai = (int)$data['nilai'];
    $kesulitan = $data['tingkat_kesulitan'];
    $mapel = $data['mata_pelajaran'];
    $gaya = $data['gaya_belajar'];
    $catatan = trim($data['catatan'] ?? '');
    $fokusMapel = subjectFocus($mapel);
    $style = styleStrategy($gaya);

    $result = [
        'program' => '',
        'level' => '',
        'color' => '',
        'bg' => '',
        'icon' => '',
        'summary' => '',
        'reasons' => [],
        'service' => [],
        'focus' => [],
        'steps' => [],
        'styleTitle' => $style['title'],
        'styleItems' => $style['items'],
        'parentNote' => '',
        'teacherNote' => ''
    ];

    if ($nilai < 60 || $kesulitan === 'Sulit') {
        $result['program'] = 'Pendampingan Intensif';
        $result['level'] = 'Butuh pendampingan dekat';
        $result['color'] = '#b91c1c';
        $result['bg'] = '#fff1f2';
        $result['icon'] = 'fa-user-check';
        $result['summary'] = 'Siswa membutuhkan bantuan belajar yang lebih dekat dan terarah. Rekomendasi ini diberikan karena nilai masih rendah atau materi dirasa sulit, sehingga pembelajaran sebaiknya dimulai dari penguatan dasar.';
        $result['reasons'] = [
            'Nilai terakhir menunjukkan materi belum dikuasai secara stabil.',
            'Tingkat kesulitan yang dipilih menunjukkan siswa membutuhkan penjelasan lebih pelan.',
            'Sistem memprioritaskan pendampingan agar hambatan belajar dapat ditemukan lebih cepat.'
        ];
        $result['service'] = [
            'Jenis layanan: kelas privat atau kelompok sangat kecil.',
            'Frekuensi saran: 2 sampai 3 kali per minggu.',
            'Pola belajar: tutor menjelaskan ulang konsep, memberi contoh soal, lalu membimbing latihan.'
        ];
        $result['focus'] = [
            'Fokus utama: ' . $fokusMapel . '.',
            'Mulai dari materi prasyarat yang belum kuat sebelum masuk ke materi baru.',
            'Setiap sesi diakhiri dengan evaluasi singkat 5 sampai 10 soal.'
        ];
        $result['steps'] = [
            'Hari 1: cek ulang materi yang paling sulit dipahami.',
            'Hari 2-3: belajar ulang konsep dasar bersama tutor.',
            'Hari 4-5: latihan soal bertahap dari mudah ke sedang.',
            'Hari 6-7: kerjakan evaluasi kecil dan catat bagian yang masih salah.'
        ];
        $result['parentNote'] = 'Orang tua disarankan memantau jadwal belajar dan memberi waktu belajar yang tenang di rumah.';
        $result['teacherNote'] = 'Tutor perlu menggunakan pendekatan pelan, banyak contoh, dan memberi umpan balik langsung.';
    } elseif ($nilai >= 60 && $nilai < 80) {
        $result['program'] = 'Program Intensif';
        $result['level'] = 'Perlu penguatan dan latihan rutin';
        $result['color'] = '#b45309';
        $result['bg'] = '#fffbeb';
        $result['icon'] = 'fa-chart-line';
        $result['summary'] = 'Siswa sudah memiliki dasar pemahaman, tetapi masih perlu latihan terarah agar hasil belajar lebih stabil. Program intensif cocok untuk memperbaiki bagian yang belum kuat dan meningkatkan kepercayaan diri.';
        $result['reasons'] = [
            'Nilai berada pada kategori cukup, namun belum maksimal.',
            'Siswa perlu latihan rutin agar kesalahan yang sama tidak berulang.',
            'Rekomendasi diarahkan pada penguatan materi dan persiapan evaluasi sekolah.'
        ];
        $result['service'] = [
            'Jenis layanan: kelas kecil atau semi privat.',
            'Frekuensi saran: 2 kali per minggu.',
            'Pola belajar: pembahasan materi singkat, latihan soal, lalu evaluasi hasil latihan.'
        ];
        $result['focus'] = [
            'Fokus utama: ' . $fokusMapel . '.',
            'Latihan soal dibuat bertahap dari level sedang menuju soal ujian.',
            'Bagian yang sering salah dicatat sebagai bahan evaluasi pertemuan berikutnya.'
        ];
        $result['steps'] = [
            'Hari 1: ulangi ringkasan materi dan rumus/poin penting.',
            'Hari 2-3: kerjakan latihan soal tipe sedang.',
            'Hari 4: bahas kesalahan bersama tutor atau catatan belajar.',
            'Hari 5-7: latihan soal campuran dan simulasi kuis singkat.'
        ];
        $result['parentNote'] = 'Orang tua cukup membantu mengingatkan jadwal belajar dan melihat hasil latihan mingguan.';
        $result['teacherNote'] = 'Tutor dapat memberi variasi soal dan membiasakan siswa menjelaskan alasan jawaban.';
    } else {
        $result['program'] = 'Program Reguler / Pengayaan';
        $result['level'] = 'Pemahaman baik, siap ditingkatkan';
        $result['color'] = '#047857';
        $result['bg'] = '#ecfdf5';
        $result['icon'] = 'fa-award';
        $result['summary'] = 'Siswa menunjukkan pemahaman yang baik. Rekomendasi diarahkan pada program reguler atau pengayaan agar kemampuan tetap terjaga dan siswa dapat mencoba soal yang lebih menantang.';
        $result['reasons'] = [
            'Nilai terakhir sudah berada pada kategori baik.',
            'Tingkat kesulitan tidak menunjukkan hambatan besar.',
            'Siswa dapat diarahkan ke latihan pengayaan agar kemampuan semakin berkembang.'
        ];
        $result['service'] = [
            'Jenis layanan: kelas reguler, pengayaan materi, atau persiapan ujian.',
            'Frekuensi saran: 1 sampai 2 kali per minggu.',
            'Pola belajar: latihan soal lanjutan, pembahasan variasi soal, dan simulasi ujian.'
        ];
        $result['focus'] = [
            'Fokus utama: ' . $fokusMapel . '.',
            'Tambahkan soal HOTS atau soal dengan variasi lebih menantang.',
            'Pertahankan konsistensi belajar agar nilai tidak turun pada evaluasi berikutnya.'
        ];
        $result['steps'] = [
            'Hari 1: review materi secara singkat.',
            'Hari 2-3: kerjakan soal pengayaan atau soal level tinggi.',
            'Hari 4-5: bahas variasi soal yang belum pernah dicoba.',
            'Hari 6-7: simulasi kuis dan evaluasi hasil belajar.'
        ];
        $result['parentNote'] = 'Orang tua dapat memberi dukungan dengan menyediakan waktu belajar rutin tanpa tekanan berlebihan.';
        $result['teacherNote'] = 'Tutor dapat memberikan tantangan soal yang lebih tinggi dan menjaga motivasi belajar siswa.';
    }

    if ($catatan !== '') {
        $result['reasons'][] = 'Catatan siswa: "' . $catatan . '"';
    }

    return $result;
}

$hasil = determineRecommendation($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rekomendasi Belajar - Portal Siswa Peta Ilmu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="siswa_portal.css" />
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
        <div class="user-avatar"><?php echo strtoupper(mb_substr($student['nama'], 0, 1)); ?></div>
        <div class="user-info">
            <div class="name"><?php echo htmlspecialchars($student['nama']); ?></div>
            <div class="role"><?php echo jenjangLabel($student['jenjang']); ?> &bull; Kelas <?php echo htmlspecialchars($student['kelas']); ?></div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="siswa_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="pendaftaran.php"><i class="fas fa-user-plus"></i> Daftar Program</a>
        <a href="input_data_belajar.php"><i class="fas fa-book"></i> Input Data Belajar</a>
        <a href="perkembangan_belajar.php"><i class="fas fa-chart-line"></i> Perkembangan Belajar</a>
        <a href="rekomendasi_belajar.php" class="active"><i class="fas fa-lightbulb"></i> Rekomendasi Belajar</a>
        <div class="nav-label" style="margin-top:8px;">Informasi</div>
        <a href="program.php"><i class="fas fa-book-open"></i> Lihat Program</a>
        <a href="kontak.php"><i class="fas fa-phone"></i> Kontak Kami</a>
        <a href="index.php"><i class="fas fa-globe"></i> Kembali ke Website</a>
    </nav>

    <div class="sidebar-footer">
        <a href="siswa_logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <h1><i class="fas fa-lightbulb" style="color:var(--primary);margin-right:6px;"></i> Rekomendasi Belajar</h1>
        <a href="input_data_belajar.php" class="site-link"><i class="fas fa-plus"></i> Input Data Baru</a>
    </div>

    <div class="page-body page-compact">
        <div class="simple-hero">
            <div>
                <p class="eyebrow">Hasil analisis belajar</p>
                <h2>Rekomendasi dibuat dari data belajar terakhir.</h2>
                <p>Decision Tree sederhana membaca nilai, tingkat kesulitan, gaya belajar, dan catatan siswa. Hasilnya bukan sekadar nama program, tetapi juga alasan dan saran belajar yang bisa langsung dipakai.</p>
            </div>
        </div>

        <?php if ($data && $hasil): ?>
            <div class="learning-summary-grid">
                <div class="mini-card"><span>Mata Pelajaran</span><strong><?php echo htmlspecialchars($data['mata_pelajaran']); ?></strong></div>
                <div class="mini-card"><span>Nilai</span><strong><?php echo htmlspecialchars($data['nilai']); ?> / 100</strong></div>
                <div class="mini-card"><span>Kesulitan</span><strong><?php echo htmlspecialchars($data['tingkat_kesulitan']); ?></strong></div>
                <div class="mini-card"><span>Gaya Belajar</span><strong><?php echo htmlspecialchars($data['gaya_belajar']); ?></strong></div>
            </div>

            <div class="recommendation-main" style="border-color: <?php echo $hasil['color']; ?>; background: <?php echo $hasil['bg']; ?>;">
                <div class="recommendation-icon" style="background: <?php echo $hasil['color']; ?>;"><i class="fas <?php echo $hasil['icon']; ?>"></i></div>
                <div>
                    <p class="eyebrow">Rekomendasi utama</p>
                    <h2 style="color: <?php echo $hasil['color']; ?>;"><?php echo $hasil['program']; ?></h2>
                    <p class="rec-level"><?php echo $hasil['level']; ?></p>
                    <p><?php echo $hasil['summary']; ?></p>
                </div>
            </div>

            <div class="recommendation-grid">
                <div class="card soft-card">
                    <div class="card-header"><div class="header-icon"><i class="fas fa-list-check"></i></div><h3>Alasan Rekomendasi</h3></div>
                    <div class="card-body">
                        <ul class="clean-list">
                            <?php foreach ($hasil['reasons'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="card soft-card">
                    <div class="card-header"><div class="header-icon"><i class="fas fa-chalkboard-user"></i></div><h3>Saran Layanan</h3></div>
                    <div class="card-body">
                        <ul class="clean-list">
                            <?php foreach ($hasil['service'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="card soft-card">
                    <div class="card-header"><div class="header-icon"><i class="fas fa-bullseye"></i></div><h3>Fokus Belajar</h3></div>
                    <div class="card-body">
                        <ul class="clean-list">
                            <?php foreach ($hasil['focus'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="card soft-card">
                    <div class="card-header"><div class="header-icon"><i class="fas fa-user-pen"></i></div><h3><?php echo htmlspecialchars($hasil['styleTitle']); ?></h3></div>
                    <div class="card-body">
                        <ul class="clean-list">
                            <?php foreach ($hasil['styleItems'] as $item): ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card soft-card plan-card">
                <div class="card-header"><div class="header-icon"><i class="fas fa-calendar-week"></i></div><h3>Rencana Belajar 7 Hari</h3></div>
                <div class="card-body">
                    <div class="step-grid">
                        <?php foreach ($hasil['steps'] as $item): ?>
                            <div class="step-item"><?php echo htmlspecialchars($item); ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="note-grid">
                <div class="note-box"><strong>Catatan untuk tutor:</strong><br><?php echo htmlspecialchars($hasil['teacherNote']); ?></div>
                <div class="note-box"><strong>Catatan untuk orang tua:</strong><br><?php echo htmlspecialchars($hasil['parentNote']); ?></div>
            </div>
        <?php else: ?>
            <div class="card soft-card">
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Belum ada data belajar. Isi data belajar terlebih dahulu agar sistem dapat memberikan rekomendasi yang sesuai.</p>
                    <a href="input_data_belajar.php"><i class="fas fa-plus"></i> Input Data Belajar</a>
                </div>
            </div>
        <?php endif; ?>
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
