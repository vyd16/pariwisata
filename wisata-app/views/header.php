<?php
/**
 * Header View - Navbar & CSS Links
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Jasa Pariwisata Terpercaya - Nikmati pengalaman liburan tak terlupakan bersama kami">
    <title>
        <?= $pageTitle ?? 'TravelMate - Jasa Pariwisata' ?>
    </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="<?= base_url() ?>" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-plane"></i>
                </div>
                <span>TravelMate</span>
            </a>

            <ul class="nav-menu">
                <li><a href="<?= base_url() ?>" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Beranda</a></li>
                <li><a href="<?= base_url() ?>#paket" class="">Paket Wisata</a></li>
                <li><a href="<?= base_url() ?>#tentang" class="">Tentang</a></li>
                <li><a href="<?= base_url() ?>#kontak" class="">Kontak</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?= base_url('booking/') ?>"
                            class="<?= strpos($_SERVER['REQUEST_URI'], 'booking') !== false ? 'active' : '' ?>">Pesanan
                            Saya</a></li>
                <?php endif; ?>
            </ul>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="d-flex align-center gap-1">
                        <?php if (isAdmin()): ?>
                            <a href="<?= base_url('admin/') ?>" class="btn btn-ghost btn-sm">
                                <i class="fas fa-cog"></i> Dashboard
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('logout.php') ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('login.php') ?>" class="btn btn-ghost btn-sm">Masuk</a>
                    <a href="<?= base_url('register.php') ?>" class="btn btn-primary btn-sm">Daftar</a>
                <?php endif; ?>
            </div>

            <button class="menu-toggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>