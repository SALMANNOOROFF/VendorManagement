<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/Worker.php';
require_once __DIR__ . '/../../classes/Notification.php';

$vendorModel = new Vendor();
$workerModel = new Worker();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: complete_profile.php'); exit; }
$workerCount = $workerModel->countByVendor($vendor['id']);

$notifModel = new Notification();
$notifs = $notifModel->getByUser($_SESSION['user_id']);

$pageTitle = 'Vendor Dashboard';
require_once __DIR__ . '/../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-grid-1x2-fill"></i> Dashboard</h1><p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?></p></div>
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-6"><div class="stat-card"><div class="stat-number"><?= ucfirst($vendor['verification_status']) ?></div><div class="stat-label">Account Status</div></div></div>
        <div class="col-md-4 col-6"><div class="stat-card"><div class="stat-number"><?= $workerCount ?></div><div class="stat-label">Total Workers</div></div></div>
        <div class="col-md-4 col-12"><div class="stat-card"><div class="stat-number"><?= htmlspecialchars($vendor['type_name']) ?></div><div class="stat-label">Company Type</div></div></div>
    </div>
    <div class="card card-vms mb-3">
        <div class="card-header"><i class="bi bi-building"></i> Company Overview</div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6"><label class="form-label">Company</label><p><strong><?= htmlspecialchars($vendor['company_name']) ?></strong></p></div>
                <div class="col-md-6"><label class="form-label">Reg No</label><p><?= htmlspecialchars($vendor['company_registration_no'] ?? 'N/A') ?></p></div>
                <div class="col-md-6"><label class="form-label">Contact</label><p><?= htmlspecialchars($vendor['primary_contact_name']) ?> — <?= htmlspecialchars($vendor['primary_contact_phone']) ?></p></div>
                <div class="col-md-6"><label class="form-label">City</label><p><?= htmlspecialchars($vendor['city']) ?>, <?= htmlspecialchars($vendor['country']) ?></p></div>
            </div>
            <a href="profile.php" class="btn btn-outline-cyan btn-sm mt-2"><i class="bi bi-pencil"></i> Edit Profile</a>
        </div>
    </div>
    
    <!-- Notifications Section -->
    <div class="card card-vms mb-3 mt-4">
        <div class="card-header"><i class="bi bi-bell-fill"></i> Recent Notifications</div>
        <div class="card-body">
            <?php if (empty($notifs)): ?>
                <p class="text-muted mb-0">No notifications found.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($notifs, 0, 5) as $n): ?>
                        <div class="list-group-item bg-transparent px-0 py-3 border-bottom d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <div class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($n['title']) ?>
                                    <?php if ($n['status'] === 'unread'): ?>
                                        <span class="badge bg-primary ms-1" style="font-size:0.65rem">NEW</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small mb-1"><?= htmlspecialchars($n['message']) ?></div>
                                <small class="text-muted-vms"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></small>
                            </div>
                            <?php if (!empty($n['link'])): ?>
                                <a href="<?= $n['link'] ?>" class="btn btn-sm btn-cyan rounded-pill px-3" onclick="markNotificationRead(<?= $n['id'] ?>)">
                                    <i class="bi bi-eye-fill"></i> Details
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($vendor['verification_status'] === 'pending'): ?>
    <div class="alert alert-vms alert-warning mt-3"><i class="bi bi-hourglass-split"></i> Your registration is <strong>pending approval</strong>. You'll be notified once reviewed.</div>
    <?php elseif ($vendor['verification_status'] === 'rejected'): ?>
    <div class="alert alert-vms alert-danger mt-3"><i class="bi bi-x-circle"></i> Your registration was <strong>rejected</strong>. <?= $vendor['rejection_reason'] ? 'Reason: ' . htmlspecialchars($vendor['rejection_reason']) : '' ?></div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
