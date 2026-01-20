<?php
/**
 * Delete Package
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAdmin();

$id = intval($_GET['id'] ?? 0);

if ($id) {
    try {
        // Get package to delete photo
        $stmt = $pdo->prepare("SELECT foto FROM paket WHERE id = ?");
        $stmt->execute([$id]);
        $package = $stmt->fetch();

        if ($package) {
            // Delete photo file
            if ($package['foto']) {
                deleteFile($package['foto'], 'uploads/packages');
            }

            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM paket WHERE id = ?");
            $stmt->execute([$id]);

            setFlash('success', 'Paket wisata berhasil dihapus');
        } else {
            setFlash('danger', 'Paket tidak ditemukan');
        }
    } catch (PDOException $e) {
        setFlash('danger', 'Gagal menghapus paket: ' . $e->getMessage());
    }
}

header('Location: ' . base_url('paket/'));
exit;
