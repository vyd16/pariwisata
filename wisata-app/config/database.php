<?php
/**
 * Database Connection using PDO
 */

require_once __DIR__ . '/../lib/env.php';

try {
    $host = env('DB_HOST', 'localhost');
    $dbname = env('DB_NAME', 'wisata_db');
    $username = env('DB_USER', 'root');
    $password = env('DB_PASS', '');

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

/**
 * Get base URL from environment
 */
function base_url($path = '')
{
    $base = env('BASE_URL', 'http://localhost/wisata-app');
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
