<?php
/**
 * Register Page
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
$success = '';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($nama) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi password tidak cocok';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'Email sudah terdaftar';
            } else {
                // Hash password and insert
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$nama, $email, $hashedPassword]);

                setFlash('success', 'Registrasi berhasil! Silakan login.');
                header('Location: ' . base_url('login.php'));
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

$pageTitle = 'Daftar - TravelMate';
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
                <h2>Buat Akun Baru</h2>
                <p class="text-muted">Bergabunglah dan nikmati berbagai paket wisata menarik</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="John Doe"
                        value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter"
                        required>
                    <small class="form-hint">Password minimal 6 karakter</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Ulangi password"
                        required>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i> Daftar
                </button>
            </form>

            <p class="text-center mt-2" style="margin-top: 1.5rem;">
                Sudah punya akun? <a href="<?= base_url('login.php') ?>">Masuk sekarang</a>
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