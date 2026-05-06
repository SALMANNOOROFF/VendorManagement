<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/Worker.php';

$vendorModel = new Vendor();
$workerModel = new Worker();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);

// If no vendor profile, redirect to complete profile
if (!$vendor) { header('Location: complete_profile.php'); exit; }

$workerCount = $workerModel->countByVendor($vendor['id']);

$pageTitle = 'Vendor Dashboard';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar_vendor.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-grid-1x2-fill text-cyan"></i> Dashboard</h1><p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?></p></div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="stat-card"><div class="stat-number"><?= ucfirst($vendor['verification_status']) ?></div><div class="stat-label">Account Status</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-number"><?= $workerCount ?></div><div class="stat-label">Total Workers</div></div></div>
        <div class="col-md-4"><div class="stat-card"><div class="stat-number"><?= htmlspecialchars($vendor['type_name']) ?></div><div class="stat-label" style="font-size:0.7rem">Company Type</div></div></div>
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

    <?php if ($vendor['verification_status'] === 'pending'): ?>
    <div class="alert alert-vms alert-warning"><i class="bi bi-hourglass-split"></i> Your registration is <strong>pending approval</strong>. You'll be notified once reviewed.</div>
    <?php elseif ($vendor['verification_status'] === 'rejected'): ?>
    <div class="alert alert-vms alert-danger"><i class="bi bi-x-circle"></i> Your registration was <strong>rejected</strong>. <?= $vendor['rejection_reason'] ? 'Reason: ' . htmlspecialchars($vendor['rejection_reason']) : '' ?></div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
