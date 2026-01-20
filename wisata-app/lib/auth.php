<?php
/**
 * Authentication & Session Management
 */

session_start();

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require user to be logged in
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

/**
 * Require user to be admin
 */
function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . base_url('index.php'));
        exit;
    }
}

/**
 * Get current user data
 */
function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['user_nama'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['role'] ?? 'user',
        'foto' => $_SESSION['user_foto'] ?? null
    ];
}

/**
 * Set user session after login
 */
function setUserSession($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nama'] = $user['nama'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_foto'] = $user['foto'];
}

/**
 * Destroy user session (logout)
 */
function destroySession()
{
    session_unset();
    session_destroy();
}

/**
 * Generate CSRF token
 */
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
