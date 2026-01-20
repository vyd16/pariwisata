<?php
/**
 * Admin Dashboard
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAdmin();

$pageTitle = 'Dashboard - Admin';

// Get statistics
$stats = [
    'users' => 0,
    'packages' => 0,
    'bookings' => 0,
    'revenue' => 0
];

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['users'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM paket");
    $stats['packages'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM booking");
    $stats['bookings'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM booking WHERE status = 'confirmed'");
    $stats['revenue'] = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Tables might not exist yet
}

// Get recent bookings
$recentBookings = [];
try {
    $stmt = $pdo->query("
        SELECT b.*, u.nama as user_nama, p.nama as paket_nama 
        FROM booking b 
        JOIN users u ON b.user_id = u.id 
        JOIN paket p ON b.paket_id = p.id 
        ORDER BY b.created_at DESC 
        LIMIT 5
    ");
    $recentBookings = $stmt->fetchAll();
} catch (PDOException $e) {
    // Tables might not exist
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
                    <h1 class="page-title">Dashboard</h1>
                    <p class="text-muted">Selamat datang,
                        <?= htmlspecialchars(getCurrentUser()['nama'] ?? 'Admin') ?>!
                    </p>
                </div>
            </div>

            <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>
                            <?= number_format($stats['users']) ?>
                        </h3>
                        <p>Total Pengguna</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon secondary">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3>
                            <?= number_format($stats['packages']) ?>
                        </h3>
                        <p>Paket Wisata</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>
                            <?= number_format($stats['bookings']) ?>
                        </h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>
                            <?= formatRupiah($stats['revenue']) ?>
                        </h3>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Aksi Cepat</h3>
                <div class="d-flex gap-1" style="flex-wrap: wrap;">
                    <a href="<?= base_url('paket/add.php') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Paket
                    </a>
                    <a href="<?= base_url('booking/') ?>" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Lihat Pesanan
                    </a>
                    <a href="<?= base_url('users/') ?>" class="btn btn-ghost">
                        <i class="fas fa-users"></i> Kelola User
                    </a>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div>
                <h3 style="margin-bottom: 1rem;">Pesanan Terbaru</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Paket</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentBookings)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 2rem;">
                                        Belum ada pesanan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td><strong>#
                                                <?= $booking['id'] ?>
                                            </strong></td>
                                        <td>
                                            <?= htmlspecialchars($booking['user_nama']) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($booking['paket_nama']) ?>
                                        </td>
                                        <td>
                                            <?= formatTanggal($booking['tanggal_berangkat']) ?>
                                        </td>
                                        <td>
                                            <?= formatRupiah($booking['total_harga']) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending' => 'badge-warning',
                                                'confirmed' => 'badge-success',
                                                'cancelled' => 'badge-danger'
                                            ][$booking['status']] ?? 'badge-info';
                                            ?>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>

</html>