<?php
/**
 * Helper Functions
 */

/**
 * Format number as Indonesian Rupiah
 */
function formatRupiah($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Generate unique ID for bookings
 */
function generateBookingId()
{
    return 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -5));
}

/**
 * Upload file with validation
 * @param array $file - $_FILES array element
 * @param string $destination - Upload directory
 * @param array $allowedTypes - Allowed MIME types
 * @param int $maxSize - Max file size in bytes (default 2MB)
 * @return array - ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadFile($file, $destination, $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'], $maxSize = 2097152)
{
    $result = ['success' => false, 'filename' => '', 'error' => ''];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Upload failed with error code: ' . $file['error'];
        return $result;
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File terlalu besar. Maksimal ' . ($maxSize / 1024 / 1024) . 'MB';
        return $result;
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        $result['error'] = 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowedTypes);
        return $result;
    }

    // Create destination directory if not exists
    $uploadPath = dirname(__DIR__) . '/' . trim($destination, '/');
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . strtolower($extension);
    $fullPath = $uploadPath . '/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Gagal memindahkan file';
    }

    return $result;
}

/**
 * Delete uploaded file
 */
function deleteFile($filename, $directory)
{
    $path = dirname(__DIR__) . '/' . trim($directory, '/') . '/' . $filename;
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

/**
 * Sanitize input string
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message helper
 */
function setFlash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format date to Indonesian format
 */
function formatTanggal($date)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $bulan[(int) date('m', $timestamp)];
    $year = date('Y', $timestamp);

    return "$day $month $year";
}

/**
 * Truncate text with ellipsis
 */
function truncate($text, $length = 100)
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}
