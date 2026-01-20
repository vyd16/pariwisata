<?php
/**
 * User Management - List View
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAdmin();

$pageTitle = 'Pengguna - Admin';

// Fetch all users
$users = [];
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('danger', 'Gagal memuat data pengguna');
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
                    <h1 class="page-title">Pengguna</h1>
                    <p class="text-muted">Kelola semua pengguna terdaftar</p>
                </div>
            </div>

            <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding: 3rem;">
                                    <i class="fas fa-users"
                                        style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                    Belum ada pengguna
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $userPhoto = $user['foto']
                                            ? base_url('uploads/users/' . $user['foto'])
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=0d9488&color=fff&size=50';
                                        ?>
                                        <img src="<?= $userPhoto ?>" alt="<?= htmlspecialchars($user['nama']) ?>"
                                            class="avatar">
                                    </td>
                                    <td><strong>
                                            <?= htmlspecialchars($user['nama']) ?>
                                        </strong></td>
                                    <td>
                                        <?= htmlspecialchars($user['email']) ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $user['role'] === 'admin' ? 'badge-primary' : 'badge-info' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= formatTanggal($user['created_at']) ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?= base_url('users/edit.php?id=' . $user['id']) ?>"
                                                class="btn btn-ghost btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>

</html>