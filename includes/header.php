<?php
// Header include — accepts $pageTitle, $hideNav (optional)
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'VMS';
$hideNav = $hideNav ?? false;
$appUrl = '/VendorM/public';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Vendor Management System</title>
    <link href="<?= $appUrl ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $appUrl ?>/assets/css/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $appUrl ?>/assets/css/custom.css" rel="stylesheet">
    <script>const APP_URL = '<?= $appUrl ?>';</script>
</head>
<body>
<?php if (!$hideNav && isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-vms">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $appUrl ?>/">
            <i class="bi bi-shield-check"></i> VMS <span>Portal</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="d-none d-md-inline nav-user-info">
                <i class="bi bi-person-circle" style="color:var(--gray-mid)"></i>
                <span style="color:var(--text-light);font-size:0.85rem"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                <span class="badge badge-active ms-1"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role_name'] ?? ''))) ?></span>
            </span>
            <a class="nav-link d-none d-md-inline" href="<?= $appUrl ?>/logout.php" style="color:var(--text-light);font-size:0.85rem">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <!-- Mobile sidebar toggle -->
            <button class="btn d-md-none" id="sidebarToggle" type="button" style="color:var(--cyan);border:1px solid var(--cyan);padding:0.25rem 0.5rem;font-size:1.1rem">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</nav>
<?php endif; ?>
