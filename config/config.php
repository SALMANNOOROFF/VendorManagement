<?php
/**
 * Application Configuration
 */

// Base URL — update for production
define('APP_URL', '/VendorM/public');
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/assets/uploads');
define('VENDOR_DOCS_PATH', UPLOAD_PATH . '/vendor_docs');
define('WORKER_DOCS_PATH', UPLOAD_PATH . '/worker_docs');

// Session config — only set if session not yet started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
}

// Error reporting — set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Karachi');

// CSRF Token Generation
function generateCSRFToken(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(string $token): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
