<?php
/**
 * Booking List - Order History
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireLogin();

$pageTitle = 'Riwayat Pesanan';
$isAdmin = isAdmin();
$userId = getCurrentUser()['id'];

// Fetch bookings
$bookings = [];
try {
    if ($isAdmin) {
        // Admin sees all bookings
        $stmt = $pdo->query("
            SELECT b.*, u.nama as user_nama, p.nama as paket_nama, p.foto as paket_foto,
                   (SELECT COUNT(*) FROM booking_detail WHERE booking_id = b.id) as jumlah_penumpang
            FROM booking b 
            JOIN users u ON b.user_id = u.id 
            JOIN paket p ON b.paket_id = p.id 
            ORDER BY b.created_at DESC
        ");
    } else {
        // User sees only their bookings
        $stmt = $pdo->prepare("
            SELECT b.*, p.nama as paket_nama, p.foto as paket_foto,
                   (SELECT COUNT(*) FROM booking_detail WHERE booking_id = b.id) as jumlah_penumpang
            FROM booking b 
            JOIN paket p ON b.paket_id = p.id 
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$userId]);
    }
    $bookings = $stmt->fetchAll();
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
    <?php if ($isAdmin): ?>
        <div class="admin-layout">
            <?php include '../views/sidebar.php'; ?>
            <main class="main-content">
            <?php else: ?>
                <?php include '../views/header.php'; ?>
                <main style="padding-top: 100px; min-height: 100vh;">
                    <div class="container">
                    <?php endif; ?>

                    <div class="page-header">
                        <div>
                            <h1 class="page-title">
                                <?= $isAdmin ? 'Semua Pesanan' : 'Pesanan Saya' ?>
                            </h1>
                            <p class="text-muted">
                                <?= $isAdmin ? 'Kelola semua pesanan wisata' : 'Riwayat pemesanan paket wisata Anda' ?>
                            </p>
                        </div>
                        <?php if (!$isAdmin): ?>
                            <a href="<?= base_url() ?>#paket" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Booking Baru
                            </a>
                        <?php endif; ?>
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
                                    <th>ID Pesanan</th>
                                    <?php if ($isAdmin): ?>
                                        <th>Pelanggan</th>
                                    <?php endif; ?>
                                    <th>Paket</th>
                                    <th>Tgl Berangkat</th>
                                    <th>Penumpang</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="<?= $isAdmin ? 8 : 7 ?>" class="text-center text-muted"
                                            style="padding: 3rem;">
                                            <i class="fas fa-calendar-times"
                                                style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                                            Belum ada pesanan
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><strong>#
                                                    <?= $booking['id'] ?>
                                                </strong></td>
                                            <?php if ($isAdmin): ?>
                                                <td>
                                                    <?= htmlspecialchars($booking['user_nama']) ?>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <div class="d-flex align-center gap-1">
                                                    <img src="<?= $booking['paket_foto'] ? base_url('uploads/packages/' . $booking['paket_foto']) : 'https://via.placeholder.com/40' ?>"
                                                        class="img-thumbnail" alt="">
                                                    <?= htmlspecialchars($booking['paket_nama']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?= formatTanggal($booking['tanggal_berangkat']) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= $booking['jumlah_penumpang'] ?> orang
                                                </span>
                                            </td>
                                            <td><strong class="text-primary">
                                                    <?= formatRupiah($booking['total_harga']) ?>
                                                </strong></td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'badge-warning',
                                                    'confirmed' => 'badge-success',
                                                    'cancelled' => 'badge-danger'
                                                ][$booking['status']] ?? 'badge-info';
                                                $statusText = [
                                                    'pending' => 'Menunggu',
                                                    'confirmed' => 'Dikonfirmasi',
                                                    'cancelled' => 'Dibatalkan'
                                                ][$booking['status']] ?? $booking['status'];
                                                ?>
                                                <span class="badge <?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="<?= base_url('booking/detail.php?id=' . $booking['id']) ?>"
                                                        class="btn btn-ghost btn-sm" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($isAdmin && $booking['status'] === 'pending'): ?>
                                                        <a href="<?= base_url('booking/process.php?action=confirm&id=' . $booking['id']) ?>"
                                                            class="btn btn-success btn-sm" title="Konfirmasi">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="<?= base_url('booking/process.php?action=cancel&id=' . $booking['id']) ?>"
                                                            class="btn btn-danger btn-sm" data-confirm="Batalkan pesanan ini?"
                                                            title="Batalkan">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($isAdmin): ?>
                </main>
        </div>
    <?php else: ?>
        </div>
        </main>
        <?php include '../views/footer.php'; ?>
    <?php endif; ?>

    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <?php if ($isAdmin): ?>
    </body>

    </html>
<?php endif; ?>