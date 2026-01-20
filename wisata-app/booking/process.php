<?php
/**
 * Booking Process - Status Update Handler
 */
require_once '../config/database.php';
require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAdmin();

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($id && in_array($action, ['confirm', 'cancel'])) {
    try {
        $status = $action === 'confirm' ? 'confirmed' : 'cancelled';

        $stmt = $pdo->prepare("UPDATE booking SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        if ($stmt->rowCount() > 0) {
            $message = $action === 'confirm' ? 'Pesanan berhasil dikonfirmasi' : 'Pesanan berhasil dibatalkan';
            setFlash('success', $message);
        } else {
            setFlash('danger', 'Pesanan tidak ditemukan');
        }
    } catch (PDOException $e) {
        setFlash('danger', 'Gagal memproses pesanan: ' . $e->getMessage());
    }
}

header('Location: ' . base_url('booking/'));
exit;
