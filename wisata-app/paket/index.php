<?php
/**
 * Package Management - List View
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAdmin();

$pageTitle = 'Paket Wisata - Admin';

// Fetch all packages
$packages = [];
try {
    $stmt = $pdo->query("SELECT * FROM paket ORDER BY created_at DESC");
    $packages = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('danger', 'Gagal memuat data paket');
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
                    <h1 class="page-title">Paket Wisata</h1>
                    <p class="text-muted">Kelola semua paket wisata</p>
                </div>
                <a href="<?= base_url('paket/add.php') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Paket
                </a>
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
                            <th>Nama Paket</th>
                            <th>Lokasi</th>
                            <th>Durasi</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($packages)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="padding: 3rem;">
                                    <i class="fas fa-box-open"
                                        style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                    Belum ada paket wisata
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($packages as $pkg): ?>
                                <tr>
                                    <td>
                                        <img src="<?= $pkg['foto'] ? base_url('uploads/packages/' . $pkg['foto']) : 'https://via.placeholder.com/50x50?text=No+Image' ?>"
                                            alt="<?= htmlspecialchars($pkg['nama']) ?>" class="img-thumbnail">
                                    </td>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($pkg['nama']) ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= truncate($pkg['deskripsi'], 50) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($pkg['lokasi']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($pkg['durasi']) ?>
                                    </td>
                                    <td><strong class="text-primary">
                                            <?= formatRupiah($pkg['harga']) ?>
                                        </strong></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="<?= base_url('paket/edit.php?id=' . $pkg['id']) ?>"
                                                class="btn btn-ghost btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('paket/delete.php?id=' . $pkg['id']) ?>"
                                                class="btn btn-danger btn-sm"
                                                data-confirm="Apakah Anda yakin ingin menghapus paket ini?" title="Hapus">
                                                <i class="fas fa-trash"></i>
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