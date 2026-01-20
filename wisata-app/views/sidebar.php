<?php
/**
 * Admin Sidebar View
 */
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!-- Sidebar Toggle Button (Mobile) -->
<button class="sidebar-toggle btn btn-ghost btn-sm" aria-label="Toggle sidebar">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?= base_url() ?>" class="logo">
            <div class="logo-icon">
                <i class="fas fa-plane"></i>
            </div>
            <span>TravelMate</span>
        </a>
    </div>

    <nav>
        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url('admin/') ?>"
                    class="<?= $currentDir === 'admin' && $currentPage === 'index' ? 'active' : '' ?>">
                    <span class="icon"><i class="fas fa-home"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('paket/') ?>" class="<?= $currentDir === 'paket' ? 'active' : '' ?>">
                    <span class="icon"><i class="fas fa-box"></i></span>
                    <span>Paket Wisata</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('booking/') ?>" class="<?= $currentDir === 'booking' ? 'active' : '' ?>">
                    <span class="icon"><i class="fas fa-calendar-check"></i></span>
                    <span>Pesanan</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('users/') ?>" class="<?= $currentDir === 'users' ? 'active' : '' ?>">
                    <span class="icon"><i class="fas fa-users"></i></span>
                    <span>Pengguna</span>
                </a>
            </li>
        </ul>

        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 1.5rem 0;">

        <ul class="sidebar-menu">
            <li>
                <a href="<?= base_url() ?>" target="_blank">
                    <span class="icon"><i class="fas fa-external-link-alt"></i></span>
                    <span>Lihat Website</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('logout.php') ?>">
                    <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Info -->
    <div style="margin-top: auto; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex align-center gap-1">
            <?php
            $user = getCurrentUser();
            $userPhoto = $user && $user['foto'] ? base_url('uploads/users/' . $user['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama'] ?? 'Admin') . '&background=0d9488&color=fff';
            ?>
            <img src="<?= $userPhoto ?>" alt="User" class="avatar">
            <div>
                <strong style="font-size: 0.9rem;">
                    <?= htmlspecialchars($user['nama'] ?? 'Admin') ?>
                </strong>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">
                    <?= $user['role'] ?? 'admin' ?>
                </p>
            </div>
        </div>
    </div>
</aside>