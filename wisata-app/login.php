<?php
/**
 * Login Page
 */
require_once 'config/database.php';
require_once 'lib/auth.php';
require_once 'lib/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . base_url('index.php'));
    exit;
}

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                setUserSession($user);

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: ' . base_url('admin/'));
                } else {
                    header('Location: ' . base_url('index.php'));
                }
                exit;
            } else {
                $error = 'Email atau password salah';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

$pageTitle = 'Login - TravelMate';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $pageTitle ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>
    <div class="auth-container">
        <div class="auth-card animate-fade-in-up">
            <div class="auth-header">
                <a href="<?= base_url() ?>" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-plane"></i>
                    </div>
                    <span>TravelMate</span>
                </a>
                <h2>Selamat Datang!</h2>
                <p class="text-muted">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <p class="text-center mt-2" style="margin-top: 1.5rem;">
                Belum punya akun? <a href="<?= base_url('register.php') ?>">Daftar sekarang</a>
            </p>

            <p class="text-center">
                <a href="<?= base_url() ?>" class="text-muted">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </p>
        </div>
    </div>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>

</html>