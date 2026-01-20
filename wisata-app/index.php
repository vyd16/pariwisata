<?php
/**
 * Landing Page - TravelMate
 */
$pageTitle = 'TravelMate - Jasa Pariwisata Terpercaya';
require_once 'views/header.php';

// Fetch featured packages (if database exists)
$packages = [];
try {
    $stmt = $pdo->query("SELECT * FROM paket ORDER BY created_at DESC LIMIT 6");
    $packages = $stmt->fetchAll();
} catch (PDOException $e) {
    // Database might not be set up yet
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80" alt="Beach Paradise">
    </div>

    <div class="container">
        <div class="hero-content animate-fade-in-up">
            <span class="hero-badge">🌴 Jelajahi Indonesia</span>
            <h1>Temukan <span>Petualangan</span> Impian Anda</h1>
            <p>Nikmati pengalaman liburan tak terlupakan dengan berbagai pilihan paket wisata eksotis. Kami siap membawa
                Anda ke destinasi terbaik Indonesia.</p>

            <div class="hero-actions">
                <a href="#paket" class="btn btn-primary btn-lg">
                    <i class="fas fa-compass"></i> Lihat Paket
                </a>
                <a href="<?= base_url('register.php') ?>" class="btn btn-outline btn-lg">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Destinasi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Paket Wisata</div>
                </div>
            </div>
        </div>

        <!-- Floating Cards -->
        <div class="floating-cards">
            <div class="float-card">
                <div class="d-flex align-center gap-1">
                    <i class="fas fa-star text-secondary"></i>
                    <span>4.9 Rating</span>
                </div>
            </div>
            <div class="float-card">
                <div class="d-flex align-center gap-1">
                    <i class="fas fa-shield-alt text-primary"></i>
                    <span>100% Aman</span>
                </div>
            </div>
            <div class="float-card">
                <div class="d-flex align-center gap-1">
                    <i class="fas fa-headset text-success"></i>
                    <span>24/7 Support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Packages Section -->
<section class="section section-lg" id="paket">
    <div class="container">
        <div class="section-header">
            <h2>Paket <span class="text-primary">Wisata Populer</span></h2>
            <p>Pilihan paket wisata terbaik untuk liburan sempurna bersama keluarga dan orang tersayang</p>
        </div>

        <div class="package-grid">
            <?php if (empty($packages)): ?>
                <!-- Sample Package Cards (when database not set up) -->
                <div class="card scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80" alt="Bali"
                        class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Pesona Bali 4D3N</h3>
                        <p class="card-text">Jelajahi keindahan Pulau Dewata dengan paket lengkap termasuk akomodasi,
                            transportasi, dan guide profesional.</p>
                        <div class="card-meta">
                            <span><i class="fas fa-clock"></i> 4 Hari 3 Malam</span>
                            <span><i class="fas fa-map-marker-alt"></i> Bali</span>
                        </div>
                        <div class="card-price">Rp 3.500.000</div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                        <a href="#" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                </div>

                <div class="card scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=600&q=80" alt="Raja Ampat"
                        class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Raja Ampat Explorer</h3>
                        <p class="card-text">Surga bawah laut Indonesia menanti. Snorkeling, diving, dan pemandangan alam
                            spektakuler.</p>
                        <div class="card-meta">
                            <span><i class="fas fa-clock"></i> 5 Hari 4 Malam</span>
                            <span><i class="fas fa-map-marker-alt"></i> Papua Barat</span>
                        </div>
                        <div class="card-price">Rp 8.500.000</div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                        <a href="#" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                </div>

                <div class="card scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1580655653885-65763b2597d0?w=600&q=80" alt="Bromo"
                        class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Sunrise Bromo Trip</h3>
                        <p class="card-text">Saksikan keajaiban matahari terbit dari kawah Bromo yang legendaris.</p>
                        <div class="card-meta">
                            <span><i class="fas fa-clock"></i> 2 Hari 1 Malam</span>
                            <span><i class="fas fa-map-marker-alt"></i> Jawa Timur</span>
                        </div>
                        <div class="card-price">Rp 1.200.000</div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                        <a href="#" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                </div>

                <div class="card scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1501179691627-eeaa65ea017c?w=600&q=80" alt="Lombok"
                        class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Lombok Adventure</h3>
                        <p class="card-text">Pink Beach, Gili Islands, dan keindahan alam Lombok dalam satu paket.</p>
                        <div class="card-meta">
                            <span><i class="fas fa-clock"></i> 4 Hari 3 Malam</span>
                            <span><i class="fas fa-map-marker-alt"></i> NTB</span>
                        </div>
                        <div class="card-price">Rp 4.200.000</div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                        <a href="#" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                </div>

                <div class="card scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1555899434-94d1368aa7af?w=600&q=80" alt="Yogyakarta"
                        class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Jogja Heritage Tour</h3>
                        <p class="card-text">Jelajahi warisan budaya Jogja: Borobudur, Prambanan, Keraton, dan kuliner
                            legendaris.</p>
                        <div class="card-meta">
                            <span><i class="fas fa-clock"></i> 3 Hari 2 Malam</span>
                            <span><i class="fas fa-map-marker-alt"></i> Yogyakarta</span>
                        </div>
                        <div class="card-price">Rp 2.800.000</div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                        <a href="#" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                </div>

                <div class="card scroll-reveal">
                    <img src="https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=600&q=80" alt="Labuan Bajo"
                        class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">Komodo Island Safari</h3>
                        <p class="card-text">Bertemu langsung dengan Komodo Dragon dan jelajahi Pulau Padar yang ikonik.</p>
                        <div class="card-meta">
                            <span><i class="fas fa-clock"></i> 4 Hari 3 Malam</span>
                            <span><i class="fas fa-map-marker-alt"></i> NTT</span>
                        </div>
                        <div class="card-price">Rp 6.500.000</div>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                        <a href="#" class="btn btn-ghost btn-sm">Detail</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($packages as $pkg): ?>
                    <div class="card scroll-reveal">
                        <img src="<?= $pkg['foto'] ? base_url('uploads/packages/' . $pkg['foto']) : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80' ?>"
                            alt="<?= htmlspecialchars($pkg['nama']) ?>" class="card-img">
                        <div class="card-body">
                            <h3 class="card-title">
                                <?= htmlspecialchars($pkg['nama']) ?>
                            </h3>
                            <p class="card-text">
                                <?= truncate(htmlspecialchars($pkg['deskripsi']), 100) ?>
                            </p>
                            <div class="card-meta">
                                <span><i class="fas fa-clock"></i>
                                    <?= htmlspecialchars($pkg['durasi']) ?>
                                </span>
                                <span><i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($pkg['lokasi']) ?>
                                </span>
                            </div>
                            <div class="card-price">
                                <?= formatRupiah($pkg['harga']) ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <?php if (isLoggedIn()): ?>
                                <a href="<?= base_url('booking/create.php?paket=' . $pkg['id']) ?>"
                                    class="btn btn-primary btn-sm">Booking</a>
                            <?php else: ?>
                                <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-sm">Booking</a>
                            <?php endif; ?>
                            <a href="<?= base_url('booking/detail.php?id=' . $pkg['id']) ?>"
                                class="btn btn-ghost btn-sm">Detail</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section" id="tentang" style="background: var(--bg-card);">
    <div class="container">
        <div class="section-header">
            <h2>Mengapa Memilih <span class="text-primary">TravelMate?</span></h2>
            <p>Kami hadir untuk memberikan pengalaman liburan terbaik dengan pelayanan profesional</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card scroll-reveal">
                <div class="stat-icon primary">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>100%</h3>
                    <p>Terpercaya & Aman</p>
                </div>
            </div>

            <div class="stat-card scroll-reveal">
                <div class="stat-icon secondary">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <h3>Best Price</h3>
                    <p>Harga Terjangkau</p>
                </div>
            </div>

            <div class="stat-card scroll-reveal">
                <div class="stat-icon success">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="stat-info">
                    <h3>24/7</h3>
                    <p>Customer Support</p>
                </div>
            </div>

            <div class="stat-card scroll-reveal">
                <div class="stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Pro Guide</h3>
                    <p>Pemandu Berpengalaman</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section section-lg" id="kontak">
    <div class="container">
        <div class="section-header">
            <h2>Hubungi <span class="text-primary">Kami</span></h2>
            <p>Tim kami siap membantu merencanakan liburan impian Anda</p>
        </div>

        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; max-width: 800px; margin: 0 auto;">
            <div class="card scroll-reveal" style="text-align: center; padding: 2rem;">
                <div class="stat-icon primary" style="margin: 0 auto 1rem;">
                    <i class="fas fa-phone"></i>
                </div>
                <h4>Telepon</h4>
                <p class="text-muted">+62 812 3456 7890</p>
            </div>

            <div class="card scroll-reveal" style="text-align: center; padding: 2rem;">
                <div class="stat-icon secondary" style="margin: 0 auto 1rem;">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h4>WhatsApp</h4>
                <p class="text-muted">+62 812 3456 7890</p>
            </div>

            <div class="card scroll-reveal" style="text-align: center; padding: 2rem;">
                <div class="stat-icon success" style="margin: 0 auto 1rem;">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4>Email</h4>
                <p class="text-muted">info@travelmate.id</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'views/footer.php'; ?>