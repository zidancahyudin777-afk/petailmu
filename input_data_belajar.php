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

$message = "";
$messageType = "success";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $mata_pelajaran = trim($_POST['mata_pelajaran'] ?? '');
    $nilai = $_POST['nilai'] ?? '';
    $tingkat_kesulitan = trim($_POST['tingkat_kesulitan'] ?? '');
    $gaya_belajar = trim($_POST['gaya_belajar'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($mata_pelajaran === '' || $nilai === '' || $tingkat_kesulitan === '' || $gaya_belajar === '') {
        $message = "Lengkapi data belajar terlebih dahulu.";
        $messageType = "error";
    } elseif (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
        $message = "Nilai harus berupa angka dari 0 sampai 100.";
        $messageType = "error";
    } else {
        if ($learningManager->addLearningData($student_id, $mata_pelajaran, $nilai, $tingkat_kesulitan, $gaya_belajar, $catatan)) {
            $message = "Data belajar berhasil disimpan. Kamu bisa melihat hasil rekomendasi pada menu Rekomendasi Belajar.";
            $messageType = "success";
        } else {
            $message = "Data belajar gagal disimpan.";
            $messageType = "error";
        }
    }
}

$dataBelajar = $learningManager->getLearningDataByStudent($_SESSION['student_id']);

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
    <title>Input Data Belajar - Portal Siswa Peta Ilmu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
        <a href="input_data_belajar.php" class="active"><i class="fas fa-book"></i> Input Data Belajar</a>
        <a href="perkembangan_belajar.php"><i class="fas fa-chart-line"></i> Perkembangan Belajar</a>
        <a href="rekomendasi_belajar.php"><i class="fas fa-lightbulb"></i> Rekomendasi Belajar</a>
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
        <h1><i class="fas fa-book" style="color:var(--primary);margin-right:6px;"></i> Input Data Belajar</h1>
        <a href="rekomendasi_belajar.php" class="site-link"><i class="fas fa-lightbulb"></i> Lihat Rekomendasi</a>
    </div>

    <div class="page-body page-compact">
        <div class="simple-hero">
            <div>
                <p class="eyebrow">Data untuk rekomendasi belajar</p>
                <h2>Isi data ini dengan sederhana, tidak perlu rumit.</h2>
                <p>Data ini digunakan sistem untuk membaca kondisi belajar kamu. Isi berdasarkan nilai terakhir dan perasaan kamu saat mempelajari materi tersebut.</p>
            </div>
        </div>

        <?php if (!empty($message)) : ?>
            <div class="message <?php echo $messageType; ?>">
                <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="margin-right: 8px;"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="input-layout">
            <div class="card soft-card">
                <div class="card-header">
                    <div class="header-icon"><i class="fas fa-pen"></i></div>
                    <h3>Form Data Belajar</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="form-portal relaxed-form">
                        <div class="form-group">
                            <label for="mata_pelajaran">Mata Pelajaran</label>
                            <p class="field-help">Pilih pelajaran yang baru kamu pelajari atau yang nilainya ingin dianalisis.</p>
                            <select name="mata_pelajaran" id="mata_pelajaran" required>
                                <option value="">Pilih mata pelajaran</option>
                                <option value="Matematika">Matematika</option>
                                <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                <option value="Bahasa Inggris">Bahasa Inggris</option>
                                <option value="IPA">IPA</option>
                                <option value="IPS">IPS</option>
                                <option value="Fisika">Fisika</option>
                                <option value="Kimia">Kimia</option>
                                <option value="Biologi">Biologi</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nilai">Nilai Terakhir</label>
                            <p class="field-help">Masukkan nilai ulangan, tugas, latihan, atau perkiraan kemampuan dari 0 sampai 100.</p>
                            <input type="number" name="nilai" id="nilai" min="0" max="100" placeholder="Contoh: 75" required>
                            <div class="score-guide">
                                <span>0-59: perlu bantuan</span>
                                <span>60-79: cukup</span>
                                <span>80-100: baik</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tingkat_kesulitan">Tingkat Kesulitan Materi</label>
                            <p class="field-help">Pilih sesuai yang kamu rasakan saat belajar materi ini.</p>
                            <select name="tingkat_kesulitan" id="tingkat_kesulitan" required>
                                <option value="">Pilih tingkat kesulitan</option>
                                <option value="Mudah">Mudah - bisa mengikuti materi</option>
                                <option value="Sedang">Sedang - paham sebagian, masih perlu latihan</option>
                                <option value="Sulit">Sulit - masih bingung dan perlu dibimbing</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="gaya_belajar">Cara Belajar yang Paling Nyaman</label>
                            <p class="field-help">Pilih cara belajar yang biasanya paling mudah kamu ikuti.</p>
                            <select name="gaya_belajar" id="gaya_belajar" required>
                                <option value="">Pilih gaya belajar</option>
                                <option value="Visual">Visual - lebih mudah lewat gambar, tulisan, tabel, atau rangkuman</option>
                                <option value="Auditori">Auditori - lebih mudah lewat penjelasan lisan dan diskusi</option>
                                <option value="Kinestetik">Kinestetik - lebih mudah lewat praktik dan latihan langsung</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="catatan">Catatan Belajar</label>
                            <p class="field-help">Tulis bagian yang masih sulit. Boleh singkat saja.</p>
                            <textarea name="catatan" id="catatan" rows="4" placeholder="Contoh: Saya masih bingung bagian rumus dan cara mengerjakan soal cerita."></textarea>
                        </div>

                        <button type="submit"><i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Data Belajar</button>
                    </form>
                </div>
            </div>

            <div class="guide-column">
                <div class="card soft-card help-card">
                    <div class="card-header"><div class="header-icon"><i class="fas fa-circle-info"></i></div><h3>Panduan Pengisian</h3></div>
                    <div class="card-body">
                        <div class="help-step"><strong>1. Nilai</strong><p>Gunakan nilai terakhir. Kalau belum ada nilai resmi, boleh pakai perkiraan kemampuan.</p></div>
                        <div class="help-step"><strong>2. Kesulitan</strong><p>Pilih dari perasaan belajar: mudah, sedang, atau sulit. Ini membantu sistem menentukan bantuan yang sesuai.</p></div>
                        <div class="help-step"><strong>3. Gaya belajar</strong><p>Pilih cara belajar yang paling nyaman, bukan yang paling keren.</p></div>
                        <div class="help-step"><strong>4. Catatan</strong><p>Tulis kendala dengan bahasa sendiri. Contoh: bingung rumus, lupa konsep, kurang latihan, atau sulit memahami bacaan.</p></div>
                    </div>
                </div>

                <div class="card soft-card help-card">
                    <div class="card-header"><div class="header-icon"><i class="fas fa-lightbulb"></i></div><h3>Contoh Pengisian</h3></div>
                    <div class="card-body">
                        <div class="example-box">
                            <p><strong>Matematika</strong> · Nilai 58 · Sulit · Visual</p>
                            <span>Rekomendasi akan cenderung ke pendampingan intensif.</span>
                        </div>
                        <div class="example-box">
                            <p><strong>Bahasa Inggris</strong> · Nilai 72 · Sedang · Auditori</p>
                            <span>Rekomendasi akan cenderung ke program intensif.</span>
                        </div>
                        <div class="example-box">
                            <p><strong>IPA</strong> · Nilai 88 · Mudah · Kinestetik</p>
                            <span>Rekomendasi akan cenderung ke reguler atau pengayaan.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card soft-card" style="margin-top: 24px;">
            <div class="card-header">
                <div class="header-icon"><i class="fas fa-history"></i></div>
                <h3>Riwayat Data Belajar</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Nilai</th>
                                <th>Kesulitan</th>
                                <th>Gaya Belajar</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($dataBelajar) > 0) : ?>
                                <?php $no = 1; ?>
                                <?php foreach ($dataBelajar as $data) : ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($data['mata_pelajaran']); ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($data['nilai']); ?></strong></td>
                                        <td><span class="badge <?php echo $data['tingkat_kesulitan'] === 'Mudah' ? 'confirmed' : ($data['tingkat_kesulitan'] === 'Sedang' ? 'pending' : 'rejected'); ?>"><?php echo htmlspecialchars($data['tingkat_kesulitan']); ?></span></td>
                                        <td><?php echo htmlspecialchars($data['gaya_belajar']); ?></td>
                                        <td><small class="text-muted"><?php echo date('d M Y', strtotime($data['tanggal_input'])); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Belum ada data belajar yang diinput.</p>
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
