<?php
/**
 * Edit User Profile
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAdmin();

$pageTitle = 'Edit Pengguna - Admin';
$error = '';

// Get user ID
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . base_url('users/'));
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        setFlash('danger', 'Pengguna tidak ditemukan');
        header('Location: ' . base_url('users/'));
        exit;
    }
} catch (PDOException $e) {
    setFlash('danger', 'Gagal memuat data');
    header('Location: ' . base_url('users/'));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    $foto = $user['foto'];

    // Validation
    if (empty($nama) || empty($email)) {
        $error = 'Nama dan email wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (!in_array($role, ['admin', 'user'])) {
        $error = 'Role tidak valid';
    } else {
        // Check if email already used by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $error = 'Email sudah digunakan pengguna lain';
        } else {
            // Handle file upload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadFile($_FILES['foto'], 'uploads/users');
                if ($upload['success']) {
                    // Delete old photo
                    if ($user['foto']) {
                        deleteFile($user['foto'], 'uploads/users');
                    }
                    $foto = $upload['filename'];
                } else {
                    $error = $upload['error'];
                }
            }

            if (empty($error)) {
                try {
                    // Build update query
                    if (!empty($password)) {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ?, password = ?, foto = ? WHERE id = ?");
                        $stmt->execute([$nama, $email, $role, $hashedPassword, $foto, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ?, foto = ? WHERE id = ?");
                        $stmt->execute([$nama, $email, $role, $foto, $id]);
                    }

                    setFlash('success', 'Data pengguna berhasil diperbarui');
                    header('Location: ' . base_url('users/'));
                    exit;
                } catch (PDOException $e) {
                    $error = 'Gagal menyimpan data: ' . $e->getMessage();
                }
            }
        }
    }

    // Update user data for form
    $user['nama'] = $nama;
    $user['email'] = $email;
    $user['role'] = $role;
}
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
    <div class="admin-layout">
        <?php include '../views/sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Pengguna</h1>
                    <p class="text-muted">Perbarui informasi pengguna</p>
                </div>
                <a href="<?= base_url('users/') ?>" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width: 600px;">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Current Photo -->
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <?php
                            $currentPhoto = $user['foto']
                                ? base_url('uploads/users/' . $user['foto'])
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=0d9488&color=fff&size=150';
                            ?>
                            <img src="<?= $currentPhoto ?>" alt="<?= htmlspecialchars($user['nama']) ?>"
                                class="avatar avatar-lg" style="width: 120px; height: 120px;">
                            <p class="text-muted" style="margin-top: 0.5rem;">
                                <?= htmlspecialchars($user['nama']) ?>
                            </p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" class="form-control"
                                value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Biarkan kosong jika tidak ingin mengubah">
                            <small class="form-hint">Minimal 6 karakter</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="foto" class="form-control"
                                accept="image/jpeg,image/png,image/webp">
                            <small class="form-hint">Format: JPG, PNG, WebP. Maksimal 2MB</small>
                        </div>

                        <div class="d-flex gap-1" style="margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?= base_url('users/') ?>" class="btn btn-ghost">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>

</html>