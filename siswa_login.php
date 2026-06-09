<?php
session_start();

if (isset($_SESSION['student_id'])) {
    header('Location: siswa_dashboard.php');
    exit;
}

require_once 'classes/StudentManager.php';
$studentManager = new StudentManager();

$message = '';
$messageType = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $emailValue = $email;

    if ($email === '' || $password === '') {
        $message = 'Email dan password wajib diisi.';
        $messageType = 'error';
    } else {
        try {
            $student = $studentManager->authenticateStudent($email, $password);

            if ($student) {
                $_SESSION['student_id'] = $student['id'];
                $_SESSION['student_nama'] = $student['nama'];
                $_SESSION['student_email'] = $student['email'];
                $_SESSION['student_jenjang'] = $student['jenjang'];
                $_SESSION['student_kelas'] = $student['kelas'];

                header('Location: siswa_dashboard.php');
                exit;
            }

            $message = 'Email atau password salah. Periksa kembali akun siswa Anda.';
            $messageType = 'error';
        } catch (Exception $e) {
            $message = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            $messageType = 'error';
            error_log('Siswa Login Error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - Peta Ilmu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #f5f6f3;
            color: #1a2438;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            width: 100%;
            background: #fff;
            border-bottom: 1px solid #dde0da;
            padding: 14px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #1a2438;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .brand img {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            object-fit: cover;
        }

        .brand .brand-name { color: #1a6b5a; }

        .back-link {
            color: #1a6b5a;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.18s;
        }

        .back-link:hover { color: #155248; }

        /* Login Area */
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        /* School Info Badge */
        .school-badge {
            text-align: center;
            margin-bottom: 24px;
        }

        .school-badge .badge-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            border-radius: 14px;
            background: #edf7f4;
            color: #1a6b5a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .school-badge h1 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1a2438;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .school-badge p {
            font-size: 0.875rem;
            color: #5f6a5a;
            line-height: 1.6;
        }

        /* Card */
        .login-card {
            background: #fff;
            border: 1px solid #dde0da;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(26,36,56,0.07);
        }

        /* Alert */
        .alert {
            padding: 11px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: flex-start;
            gap: 9px;
            line-height: 1.5;
        }

        .alert.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert i { margin-top: 1px; flex-shrink: 0; }

        /* Form */
        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #2c3527;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .input-box { position: relative; }

        .input-box .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.875rem;
            pointer-events: none;
        }

        .input-box input {
            width: 100%;
            padding: 11px 13px 11px 40px;
            border: 1.5px solid #dde0da;
            border-radius: 8px;
            background: #f5f6f3;
            color: #1a2438;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.18s;
        }

        .input-box input:hover { background: #fff; border-color: #c4c9be; }

        .input-box input:focus {
            outline: none;
            background: #fff;
            border-color: #1a6b5a;
            box-shadow: 0 0 0 3px rgba(26,107,90,0.1);
        }

        .hint {
            margin-top: 4px;
            font-size: 0.75rem;
            color: #5f6a5a;
        }

        .btn-login {
            width: 100%;
            margin-top: 8px;
            padding: 12px 16px;
            border: none;
            border-radius: 8px;
            background: #1a6b5a;
            color: #fff;
            font-family: inherit;
            font-size: 0.925rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.18s;
        }

        .btn-login:hover { background: #155248; }

        .divider {
            text-align: center;
            color: #9ca3af;
            font-size: 0.78rem;
            margin: 18px 0;
            position: relative;
        }

        .divider::before, .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 42%;
            height: 1px;
            background: #dde0da;
        }

        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .bottom-links {
            text-align: center;
            font-size: 0.85rem;
            color: #5f6a5a;
        }

        .bottom-links a {
            color: #1a6b5a;
            text-decoration: none;
            font-weight: 700;
        }

        .bottom-links a:hover { text-decoration: underline; }

        /* Demo Box */
        .demo-box {
            margin-top: 24px;
            padding: 14px 16px;
            border-radius: 10px;
            background: #f0f2ee;
            border: 1px solid #dde0da;
            color: #5f6a5a;
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .demo-box .demo-title {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 700;
            color: #2c3527;
            margin-bottom: 6px;
            font-size: 0.82rem;
        }

        .demo-row {
            display: flex;
            gap: 6px;
        }

        .demo-row .label { color: #9ca3af; }
        .demo-row code {
            background: #fff;
            border: 1px solid #dde0da;
            border-radius: 4px;
            padding: 1px 6px;
            font-size: 0.78rem;
            color: #1a6b5a;
            font-family: 'Courier New', monospace;
        }

        @media (max-width: 480px) {
            .topbar { padding: 12px 16px; }
            .login-card { padding: 22px 18px; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand">
        <img src="images/IMG_3898.PNG" alt="Logo Peta Ilmu">
        <span>Peta <span class="brand-name">Ilmu</span></span>
    </a>
    <a href="index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
    </a>
</header>

<main class="login-section">
    <div class="login-box">

        <div class="school-badge">
            <div class="badge-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h1>Login Siswa</h1>
            <p>Masuk ke portal belajar Bimbingan Belajar Peta Ilmu</p>
        </div>

        <div class="login-card">

            <?php if ($message !== ''): ?>
                <div class="alert <?php echo htmlspecialchars($messageType); ?>">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="siswa_login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Siswa</label>
                    <div class="input-box">
                        <i class="fas fa-envelope input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="email@contoh.com"
                            value="<?php echo htmlspecialchars($emailValue); ?>"
                            required
                            autocomplete="email"
                        >
                    </div>
                    <div class="hint">Email yang didaftarkan saat pendaftaran siswa</div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-box">
                        <i class="fas fa-lock input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="divider">atau</div>

            <div class="bottom-links">
                Belum punya akun? <a href="pendaftaran.php">Daftar Program Sekarang</a>
            </div>
        </div>

        <div class="demo-box">
            <div class="demo-title">
                <i class="fas fa-info-circle"></i>
                Akun contoh untuk uji coba
            </div>
            <div class="demo-row">
                <span class="label">Email</span>
                <code>siswa@example.com</code>
            </div>
            <div class="demo-row">
                <span class="label">Password</span>
                <code>siswa123</code>
            </div>
        </div>

    </div>
</main>

</body>
</html>
