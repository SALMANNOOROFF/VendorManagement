<?php
// Header include — accepts $pageTitle, $hideNav, $navLinks (optional)
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'VMS';
$hideNav = $hideNav ?? false;
$appUrl = '/VendorM/public';
$assetUrl = '/VendorM';

$currentUser = null;
$unreadNotifsCount = 0;
$allNotifs = [];

if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../classes/User.php';
    $userObj = new User();
    $currentUser = $userObj->getById($_SESSION['user_id']);

    require_once __DIR__ . '/../classes/Notification.php';
    $notifObj = new Notification();
    $unreadNotifsCount = $notifObj->getUnreadCount($_SESSION['user_id']);
    $allNotifs = $notifObj->getByUser($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Vendor Management System</title>
    <link href="<?= $assetUrl ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $assetUrl ?>/assets/css/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $assetUrl ?>/assets/css/custom.css" rel="stylesheet">
    <script>const APP_URL = '<?= $appUrl ?>';</script>
</head>
<body>
<?php if (!$hideNav && isset($_SESSION['user_id'])): ?>
<nav class="navbar-vms">
    <div class="container-fluid d-flex align-items-center justify-content-between position-relative" style="min-height: 56px;">
        
        <!-- Left: Hamburger Toggle and Page Navigation -->
        <div class="d-flex align-items-center" style="z-index: 10;">
            <button class="navbar-toggler-vms me-2" id="navToggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            <?php if (!empty($navLinks)): ?>
            <ul class="nav-links" id="navLinks">
                <?php
                $current = basename($_SERVER['PHP_SELF']);
                foreach ($navLinks as $item):
                    if (isset($item['section'])):
                ?>
                <li class="nav-section-label d-md-none"><?= $item['section'] ?></li>
                <?php else:
                    $isActive = false;
                    if (!empty($item['match'])) {
                        foreach ((array)$item['match'] as $m) {
                            if (strpos($_SERVER['PHP_SELF'], $m) !== false) { $isActive = true; break; }
                        }
                    } else {
                        $isActive = ($current === basename($item['href']));
                    }
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= $item['href'] ?>">
                        <i class="bi <?= $item['icon'] ?>"></i>
                        <span class="nav-label-text"><?= htmlspecialchars($item['label']) ?></span>
                    </a>
                </li>
                <?php endif; endforeach; ?>
                <li class="nav-mobile-user">
                    <span style="color:#cbd5e1;font-size:0.8rem">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
                        <span class="badge badge-active ms-1"><?= ucfirst(str_replace('_',' ',$_SESSION['role_name'] ?? '')) ?></span>
                    </span>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#profileModal" class="nav-link text-white ps-0 mt-2"><i class="bi bi-person-circle"></i> Profile</a>
                    <a href="<?= $appUrl ?>/logout.php" class="btn-logout mt-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
            <?php endif; ?>
        </div>

        <!-- Center: Logo -->
        <a class="navbar-brand position-absolute start-50 translate-middle-x" href="<?= $appUrl ?>/" style="z-index: 9;">
            <i class="bi bi-shield-check"></i> VMS <span>Portal</span>
        </a>

        <!-- Right: Notifications, Profile Modal Trigger, and Logout -->
        <div class="d-flex align-items-center gap-3" style="z-index: 10;">
            
            <!-- Notification Bell Icon (For Vendors) -->
            <?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'vendor'): ?>
                <a href="#" class="position-relative text-white me-2" data-bs-toggle="modal" data-bs-target="#notificationsModal" style="font-size: 1.2rem;">
                    <i class="bi bi-bell-fill" style="color: #cbd5e1; transition: var(--transition);"></i>
                    <?php if ($unreadNotifsCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger notif-badge" style="font-size: 0.55rem; padding: 0.25em 0.5em;">
                            <?= $unreadNotifsCount ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <!-- Desktop Profile Trigger -->
            <div class="d-none d-md-flex align-items-center gap-3 nav-desktop-right">
                <a class="d-flex align-items-center gap-2 p-0 text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#profileModal" style="cursor: pointer; font-weight: 500; color: #cbd5e1;">
                    <i class="bi bi-person-circle" style="color: var(--primary-light); font-size: 1.25rem;"></i>
                    <span style="transition: var(--transition);">Profile</span>
                </a>
                <a class="btn-logout" href="<?= $appUrl ?>/logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
            
        </div>
    </div>
</nav>

<!-- Profile Modal -->
<?php if ($currentUser): ?>
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="profileModalLabel">
                    <i class="bi bi-person-circle text-primary me-2"></i> Account Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle mb-3" style="width: 80px; height: 80px; font-size: 3rem; border: 3px solid var(--primary-light);">
                        <i class="bi bi-person"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($currentUser['username'] ?? '') ?></h4>
                    <span class="badge bg-primary rounded-pill px-3 py-1"><?= htmlspecialchars($currentUser['role_display'] ?? '') ?></span>
                </div>
                
                <hr class="my-3 opacity-25">
                
                <div class="row g-3">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <span class="text-muted fw-semibold"><i class="bi bi-envelope me-2 text-primary"></i> Email:</span>
                        <span class="text-dark fw-bold"><?= htmlspecialchars($currentUser['email'] ?? '') ?></span>
                    </div>
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <span class="text-muted fw-semibold"><i class="bi bi-check-circle me-2 text-success"></i> Status:</span>
                        <span class="badge bg-success rounded-pill px-2 py-1"><?= ucfirst($currentUser['status'] ?? 'Active') ?></span>
                    </div>
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <span class="text-muted fw-semibold"><i class="bi bi-calendar-plus me-2 text-primary"></i> Registered At:</span>
                        <span class="text-dark fw-bold"><?= !empty($currentUser['created_at']) ? date('d M Y, h:i A', strtotime($currentUser['created_at'])) : 'N/A' ?></span>
                    </div>
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <span class="text-muted fw-semibold"><i class="bi bi-shield-fill-check me-2 text-success"></i> Approved At:</span>
                        <span class="text-dark fw-bold"><?= !empty($currentUser['approved_at']) ? date('d M Y, h:i A', strtotime($currentUser['approved_at'])) : 'N/A' ?></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-primary-vms rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Notifications Dropdown Modal -->
<?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'vendor'): ?>
<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="notificationsModalLabel">
                    <i class="bi bi-bell text-primary me-2"></i> Notifications
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Recent updates</span>
                    <?php if ($unreadNotifsCount > 0): ?>
                        <button onclick="markAllNotificationsAsRead()" class="btn btn-link text-primary btn-sm p-0 text-decoration-none fw-semibold">
                            Mark all as read
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="notif-list">
                    <?php if (empty($allNotifs)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">No notifications found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($allNotifs as $n): ?>
                            <div class="p-3 mb-2 rounded border-start border-4 <?= $n['status'] === 'unread' ? 'bg-light border-primary' : 'bg-white border-secondary' ?> d-flex justify-content-between align-items-start gap-2 notif-item" data-id="<?= $n['id'] ?>" style="box-shadow: var(--shadow-sm);">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark small mb-1 d-flex align-items-center gap-1">
                                        <?= htmlspecialchars($n['title']) ?>
                                        <?php if ($n['status'] === 'unread'): ?>
                                            <span class="badge bg-primary px-1" style="font-size:0.6rem">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted mb-2" style="font-size: 0.8rem; line-height: 1.4;">
                                        <?= htmlspecialchars($n['message']) ?>
                                    </div>
                                    <div class="text-muted-vms extra-small" style="font-size: 0.7rem;">
                                        <?= date('d M Y, h:i A', strtotime($n['created_at'])) ?>
                                    </div>
                                </div>
                                <?php if (!empty($n['link'])): ?>
                                    <a href="<?= $n['link'] ?>" class="btn btn-sm btn-cyan rounded-pill px-3 py-1 flex-shrink-0 align-self-center btn-notif-details" onclick="markNotificationRead(<?= $n['id'] ?>)">
                                        <i class="bi bi-eye-fill"></i> Details
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-primary-vms rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>
