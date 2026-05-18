<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['approver','super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
$v = new Vendor();
$stats = [
    'pending'  => $v->count(['verification_status' => 'pending']),
    'verified' => $v->count(['verification_status' => 'verified']),
    'rejected' => $v->count(['verification_status' => 'rejected']),
];
$pageTitle = 'Approver Dashboard';
require_once __DIR__ . '/../../includes/sidebar_approver.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-grid-1x2-fill"></i> Approver Dashboard</h1><p>Review and manage vendor registrations</p></div>
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-6"><div class="stat-card stat-warning"><div class="stat-number"><?= $stats['pending'] ?></div><div class="stat-label">Pending Review</div></div></div>
        <div class="col-md-4 col-6"><div class="stat-card stat-success"><div class="stat-number"><?= $stats['verified'] ?></div><div class="stat-label">Approved</div></div></div>
        <div class="col-md-4 col-12"><div class="stat-card stat-danger"><div class="stat-number"><?= $stats['rejected'] ?></div><div class="stat-label">Rejected</div></div></div>
    </div>
    <?php if ($stats['pending'] > 0): ?>
    <div class="alert alert-vms alert-warning"><i class="bi bi-exclamation-triangle"></i> You have <strong><?= $stats['pending'] ?></strong> vendor(s) awaiting review. <a href="pending.php">Review now →</a></div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
