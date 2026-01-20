<?php
/**
 * Booking Detail - Invoice View
 * Shows Master (Booking) and Detail (Passengers)
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireLogin();

$pageTitle = 'Detail Pesanan';
$id = intval($_GET['id'] ?? 0);
$userId = getCurrentUser()['id'];
$isAdmin = isAdmin();

// Fetch booking with package info
$booking = null;
$passengers = [];

try {
    if ($isAdmin) {
        $stmt = $pdo->prepare("
            SELECT b.*, u.nama as user_nama, u.email as user_email, u.telepon as user_telepon,
                   p.nama as paket_nama, p.deskripsi as paket_deskripsi, p.harga as paket_harga,
                   p.durasi as paket_durasi, p.lokasi as paket_lokasi, p.foto as paket_foto
            FROM booking b 
            JOIN users u ON b.user_id = u.id 
            JOIN paket p ON b.paket_id = p.id 
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT b.*, p.nama as paket_nama, p.deskripsi as paket_deskripsi, p.harga as paket_harga,
                   p.durasi as paket_durasi, p.lokasi as paket_lokasi, p.foto as paket_foto
            FROM booking b 
            JOIN paket p ON b.paket_id = p.id 
            WHERE b.id = ? AND b.user_id = ?
        ");
        $stmt->execute([$id, $userId]);
    }
    $booking = $stmt->fetch();

    if ($booking) {
        // Fetch passengers (detail)
        $stmt = $pdo->prepare("SELECT * FROM booking_detail WHERE booking_id = ?");
        $stmt->execute([$id]);
        $passengers = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    // Error fetching
}

if (!$booking) {
    setFlash('danger', 'Pesanan tidak ditemukan');
    header('Location: ' . base_url('booking/'));
    exit;
}

$statusClass = [
    'pending' => 'badge-warning',
    'confirmed' => 'badge-success',
    'cancelled' => 'badge-danger'
][$booking['status']] ?? 'badge-info';

$statusText = [
    'pending' => 'Menunggu Konfirmasi',
    'confirmed' => 'Dikonfirmasi',
    'cancelled' => 'Dibatalkan'
][$booking['status']] ?? $booking['status'];
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
    <style>
        .invoice-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 2rem;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            color: white;
        }

        .invoice-body {
            padding: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .info-item label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .info-item strong {
            font-size: 1rem;
        }

        .passenger-list {
            margin-top: 2rem;
        }

        .passenger-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .passenger-item .number {
            background: var(--primary);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .total-section {
            background: rgba(13, 148, 136, 0.1);
            border-top: 2px solid var(--primary);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
                color: black;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body>
    <?php include '../views/header.php'; ?>

    <main style="padding-top: 100px; min-height: 100vh;">
        <div class="container" style="max-width: 900px;">
            <div class="page-header no-print">
                <div>
                    <h1 class="page-title">Detail Pesanan #
                        <?= $booking['id'] ?>
                    </h1>
                    <p class="text-muted">Invoice pemesanan paket wisata</p>
                </div>
                <div class="d-flex gap-1">
                    <button onclick="window.print()" class="btn btn-ghost">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                    <a href="<?= base_url('booking/') ?>" class="btn btn-ghost">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?> no-print">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <div class="card" style="overflow: hidden;">
                <!-- Invoice Header -->
                <div class="invoice-header">
                    <div class="d-flex justify-between align-center" style="flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h2 style="margin-bottom: 0.5rem;">TravelMate</h2>
                            <p style="opacity: 0.8; margin-bottom: 0;">Jasa Pariwisata Terpercaya</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin-bottom: 0.25rem;">Invoice #
                                <?= $booking['id'] ?>
                            </p>
                            <p style="opacity: 0.8; margin-bottom: 0;">
                                <?= formatTanggal($booking['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Body -->
                <div class="invoice-body">
                    <!-- Status Badge -->
                    <div style="margin-bottom: 1.5rem;">
                        <span class="badge <?= $statusClass ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?= $statusText ?>
                        </span>
                    </div>

                    <!-- Booking Info -->
                    <div class="info-grid">
                        <?php if ($isAdmin && isset($booking['user_nama'])): ?>
                            <div class="info-item">
                                <label>Nama Pemesan</label>
                                <strong>
                                    <?= htmlspecialchars($booking['user_nama']) ?>
                                </strong>
                            </div>
                            <div class="info-item">
                                <label>Email</label>
                                <strong>
                                    <?= htmlspecialchars($booking['user_email']) ?>
                                </strong>
                            </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <label>Paket Wisata</label>
                            <strong>
                                <?= htmlspecialchars($booking['paket_nama']) ?>
                            </strong>
                        </div>
                        <div class="info-item">
                            <label>Lokasi</label>
                            <strong>
                                <?= htmlspecialchars($booking['paket_lokasi']) ?>
                            </strong>
                        </div>
                        <div class="info-item">
                            <label>Durasi</label>
                            <strong>
                                <?= htmlspecialchars($booking['paket_durasi']) ?>
                            </strong>
                        </div>
                        <div class="info-item">
                            <label>Tanggal Berangkat</label>
                            <strong>
                                <?= formatTanggal($booking['tanggal_berangkat']) ?>
                            </strong>
                        </div>
                    </div>

                    <!-- Passenger List (Detail) -->
                    <div class="passenger-list">
                        <h3 style="margin-bottom: 1rem;">
                            <i class="fas fa-users text-primary"></i> Daftar Penumpang (
                            <?= count($passengers) ?> orang)
                        </h3>

                        <?php foreach ($passengers as $index => $p): ?>
                            <div class="passenger-item">
                                <span class="number">
                                    <?= $index + 1 ?>
                                </span>
                                <div style="flex: 1;">
                                    <strong>
                                        <?= htmlspecialchars($p['nama_penumpang']) ?>
                                    </strong>
                                    <?php if ($p['no_identitas']): ?>
                                        <br><small class="text-muted">ID:
                                            <?= htmlspecialchars($p['no_identitas']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($p['telepon']): ?>
                                    <span class="text-muted">
                                        <i class="fas fa-phone"></i>
                                        <?= htmlspecialchars($p['telepon']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Total Section -->
                <div class="total-section">
                    <div>
                        <p class="text-muted" style="margin-bottom: 0;">
                            <?= count($passengers) ?> penumpang ×
                            <?= formatRupiah($booking['paket_harga']) ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <p class="text-muted" style="margin-bottom: 0.25rem;">Total Pembayaran</p>
                        <h2 style="margin-bottom: 0; color: var(--secondary);">
                            <?= formatRupiah($booking['total_harga']) ?>
                        </h2>
                    </div>
                </div>
            </div>

            <?php if ($isAdmin && $booking['status'] === 'pending'): ?>
                <div class="d-flex gap-1 justify-center" style="margin-top: 1.5rem;">
                    <a href="<?= base_url('booking/process.php?action=confirm&id=' . $booking['id']) ?>"
                        class="btn btn-success btn-lg">
                        <i class="fas fa-check"></i> Konfirmasi Pesanan
                    </a>
                    <a href="<?= base_url('booking/process.php?action=cancel&id=' . $booking['id']) ?>"
                        class="btn btn-danger btn-lg" data-confirm="Batalkan pesanan ini?">
                        <i class="fas fa-times"></i> Batalkan
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../views/footer.php'; ?>