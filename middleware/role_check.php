<?php
// Middleware: Authentication & Role Check
function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /VendorM/public/login.php');
        exit();
    }
}

function checkRole(array $allowedRoles): void {
    requireAuth();
    if (!in_array($_SESSION['role_name'], $allowedRoles)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>403</title><style>body{background:#0A1628;color:#fff;font-family:Inter,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}.c{text-align:center}h1{font-size:6rem;color:#00BCD4;margin:0}p{color:#B0BEC5}a{color:#00BCD4}</style></head><body><div class="c"><h1>403</h1><p>Access Denied</p><a href="/VendorM/public/">Return Home</a></div></body></html>';
        exit();
    }
}
