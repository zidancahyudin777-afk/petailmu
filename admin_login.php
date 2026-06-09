<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

require_once 'config/database.php';
require_once 'classes/AdminManager.php';

$database = new Database();
$pdo = $database->getConnection();
$adminManager = new AdminManager();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $message = 'Username dan password wajib diisi!';
        $messageType = 'error';
    } else {
        try {
            $admin = $adminManager->authenticateAdmin($username, $password);
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $message = 'Username atau password salah!';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'Terjadi kesalahan: ' . $e->getMessage();
            $messageType = 'error';
            error_log('Admin Login Error: ' . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - Bimbingan Belajar Peta Ilmu</title>
    <link rel="stylesheet" href="stylemain.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .topbar-logo img { height: 36px; border-radius: 6px; }
        .topbar-logo span { color: #fff; font-weight: 700; font-size: 1.25rem; letter-spacing: -0.01em; }
        .topbar-home {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            padding: 6px 14px;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .topbar-home:hover { color: #fff; background-color: rgba(255, 255, 255, 0.1); }

        /* ── Login Container ── */
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 400px;
            padding: 40px 32px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Icon Header ── */
        .login-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
        }
        .login-icon i { color: #fff; font-size: 1.4rem; }

        .login-card h2 {
            text-align: center;
            color: #020617;
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 24px;
        }

        /* ── Alert Message ── */
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            font-weight: 500;
            text-align: center;
        }
        .message.error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* ── Form Groups ── */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i.input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
            color: #0f172a;
            background-color: #f8fafc;
        }
        .form-group input:focus {
            outline: none;
            background-color: #fff;
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }
        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.3);
        }
        .submit-btn:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <a href="index.php" class="topbar-logo">
            <img src="images/IMG_3898.PNG" alt="Logo Peta Ilmu" />
            <span>Peta Ilmu</span>
        </a>
        <a href="index.php" class="topbar-home">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Login Card -->
    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-icon">
                <i class="fas fa-user-shield"></i>
            </div>

            <h2>Admin Login</h2>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Masukkan username"
                            required 
                        />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password"
                            required 
                        />
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

        </div>
    </div>
</body>
</html>