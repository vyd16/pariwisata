<?php
/**
 * Create Booking - Master-Detail Form
 * Master: Booking information
 * Detail: Passenger information (dynamic)
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireLogin();

$pageTitle = 'Buat Pesanan Baru';
$error = '';
$userId = getCurrentUser()['id'];

// Get package ID from URL
$paketId = intval($_GET['paket'] ?? 0);

// Fetch package details
$package = null;
if ($paketId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM paket WHERE id = ?");
        $stmt->execute([$paketId]);
        $package = $stmt->fetch();
    } catch (PDOException $e) {
        // Package not found
    }
}

// Fetch all packages for dropdown
$packages = [];
try {
    $stmt = $pdo->query("SELECT id, nama, harga, durasi, lokasi FROM paket ORDER BY nama");
    $packages = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Gagal memuat paket wisata';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paketId = intval($_POST['paket_id'] ?? 0);
    $tanggalBerangkat = $_POST['tanggal_berangkat'] ?? '';
    $penumpang = $_POST['penumpang'] ?? [];
    
    // Validation
    if (!$paketId || empty($tanggalBerangkat) || empty($penumpang)) {
        $error = 'Semua data wajib diisi';
    } elseif (strtotime($tanggalBerangkat) < strtotime('today')) {
        $error = 'Tanggal berangkat tidak valid';
    } else {
        // Get package price
        $stmt = $pdo->prepare("SELECT harga FROM paket WHERE id = ?");
        $stmt->execute([$paketId]);
        $pkg = $stmt->fetch();
        
        if (!$pkg) {
            $error = 'Paket tidak ditemukan';
        } else {
            $hargaPerOrang = $pkg['harga'];
            $jumlahPenumpang = count($penumpang);
            $totalHarga = $hargaPerOrang * $jumlahPenumpang;
            
            try {
                // Start transaction
                $pdo->beginTransaction();
                
                // Insert master (booking)
                $stmt = $pdo->prepare("INSERT INTO booking (user_id, paket_id, tanggal_berangkat, total_harga, status) VALUES (?, ?, ?, ?, 'pending')");
                $stmt->execute([$userId, $paketId, $tanggalBerangkat, $totalHarga]);
                $bookingId = $pdo->lastInsertId();
                
                // Insert details (passengers)
                $stmt = $pdo->prepare("INSERT INTO booking_detail (booking_id, nama_penumpang, no_identitas, telepon) VALUES (?, ?, ?, ?)");
                
                foreach ($penumpang as $p) {
                    if (!empty($p['nama'])) {
                        $stmt->execute([
                            $bookingId,
                            sanitize($p['nama']),
                            sanitize($p['no_identitas'] ?? ''),
                            sanitize($p['telepon'] ?? '')
                        ]);
                    }
                }
                
                // Commit transaction
                $pdo->commit();
                
                setFlash('success', 'Pesanan berhasil dibuat! Silakan tunggu konfirmasi dari admin.');
                header('Location: ' . base_url('booking/detail.php?id=' . $bookingId));
                exit;
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = 'Gagal menyimpan pesanan: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .package-preview {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .package-preview img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--radius-md);
        }
        .package-info h4 {
            margin-bottom: 0.5rem;
        }
        .price-summary {
            background: rgba(13, 148, 136, 0.1);
            border: 1px solid var(--primary);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include '../views/header.php'; ?>
    
    <main style="padding-top: 100px; min-height: 100vh;">
        <div class="container" style="max-width: 900px;">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Buat Pesanan</h1>
                    <p class="text-muted">Isi formulir untuk memesan paket wisata</p>
                </div>
                <a href="<?= base_url('booking/') ?>" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" id="bookingForm">
                        <!-- Section: Package Selection -->
                        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                            <span style="background: var(--primary); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">1</span>
                            Pilih Paket Wisata
                        </h3>
                        
                        <div class="form-group">
                            <label class="form-label">Paket Wisata *</label>
                            <select name="paket_id" id="paketSelect" class="form-control" required onchange="updatePackagePreview()">
                                <option value="">-- Pilih Paket --</option>
                                <?php foreach ($packages as $pkg): ?>
                                    <option value="<?= $pkg['id'] ?>" 
                                            data-harga="<?= $pkg['harga'] ?>"
                                            data-durasi="<?= htmlspecialchars($pkg['durasi']) ?>"
                                            data-lokasi="<?= htmlspecialchars($pkg['lokasi']) ?>"
                                            <?= $paketId == $pkg['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pkg['nama']) ?> - <?= formatRupiah($pkg['harga']) ?>/orang
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="packagePreview" class="package-preview" style="<?= $package ? '' : 'display: none;' ?>">
                            <?php if ($package): ?>
                                <img src="<?= $package['foto'] ? base_url('uploads/packages/' . $package['foto']) : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=200' ?>" alt="">
                                <div class="package-info">
                                    <h4><?= htmlspecialchars($package['nama']) ?></h4>
                                    <p class="text-muted" style="margin-bottom: 0.5rem;">
                                        <i class="fas fa-clock"></i> <?= htmlspecialchars($package['durasi']) ?> &nbsp;
                                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($package['lokasi']) ?>
                                    </p>
                                    <strong class="text-secondary"><?= formatRupiah($package['harga']) ?>/orang</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Berangkat *</label>
                            <input type="date" name="tanggal_berangkat" class="form-control" 
                                   min="<?= date('Y-m-d') ?>" 
                                   value="<?= htmlspecialchars($_POST['tanggal_berangkat'] ?? '') ?>" required>
                        </div>
                        
                        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 2rem 0;">
                        
                        <!-- Section: Passengers (Detail) -->
                        <div class="d-flex justify-between align-center" style="margin-bottom: 1rem;">
                            <h3 style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <span style="background: var(--primary); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">2</span>
                                Data Penumpang
                            </h3>
                            <button type="button" id="addPassenger" class="btn btn-secondary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Penumpang
                            </button>
                        </div>
                        
                        <p class="text-muted" style="margin-bottom: 1rem;">
                            <i class="fas fa-info-circle"></i> Tambahkan data semua penumpang yang akan berangkat
                        </p>
                        
                        <div id="passengerContainer">
                            <!-- First passenger (required) -->
                            <div class="passenger-form">
                                <div class="passenger-header">
                                    <span class="passenger-number">1</span>
                                    <strong>Penumpang 1</strong>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lengkap *</label>
                                        <input type="text" name="penumpang[1][nama]" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">No. Identitas (KTP/Paspor)</label>
                                        <input type="text" name="penumpang[1][no_identitas]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">No. Telepon</label>
                                        <input type="tel" name="penumpang[1][telepon]" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Price Summary -->
                        <div class="price-summary">
                            <div class="d-flex justify-between align-center">
                                <div>
                                    <p class="text-muted" style="margin-bottom: 0.25rem;">Total Pembayaran</p>
                                    <h2 id="totalPrice" style="margin-bottom: 0; color: var(--secondary);">Rp 0</h2>
                                    <input type="hidden" name="total_harga" id="totalPriceInput" value="0">
                                </div>
                                <div style="text-align: right;">
                                    <p class="text-muted" style="margin-bottom: 0;">
                                        <span id="passengerCount">1</span> penumpang × <span id="pricePerPerson">Rp 0</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-1" style="margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Konfirmasi Pesanan
                            </button>
                            <a href="<?= base_url() ?>" class="btn btn-ghost btn-lg">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <?php include '../views/footer.php'; ?>
    
    <script>
        // Store price per person
        let pricePerPerson = <?= $package['harga'] ?? 0 ?>;
        
        function updatePackagePreview() {
            const select = document.getElementById('paketSelect');
            const preview = document.getElementById('packagePreview');
            const option = select.options[select.selectedIndex];
            
            if (option.value) {
                pricePerPerson = parseInt(option.dataset.harga);
                document.getElementById('pricePerPerson').textContent = formatRupiah(pricePerPerson);
                updateTotal();
            }
        }
        
        function updateTotal() {
            const count = document.querySelectorAll('.passenger-form').length;
            const total = pricePerPerson * count;
            
            document.getElementById('passengerCount').textContent = count;
            document.getElementById('totalPrice').textContent = formatRupiah(total);
            document.getElementById('totalPriceInput').value = total;
        }
        
        // Watch for passenger changes
        const observer = new MutationObserver(updateTotal);
        observer.observe(document.getElementById('passengerContainer'), { childList: true });
        
        // Initial calculation
        document.addEventListener('DOMContentLoaded', function() {
            updatePackagePreview();
            updateTotal();
        });
    </script>
