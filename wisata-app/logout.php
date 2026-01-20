<?php
/**
 * Logout Handler
 */
require_once 'config/database.php';
require_once 'lib/auth.php';

// Destroy session
destroySession();

// Redirect to home
header('Location: ' . base_url('index.php'));
exit;
