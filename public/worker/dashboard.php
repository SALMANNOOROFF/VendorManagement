<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['worker']);
require_once __DIR__ . '/../../classes/Worker.php';
$workerModel = new Worker();
$w = $workerModel->getByUserId($_SESSION['user_id']);
$pageTitle = 'Worker Dashboard';
require_once __DIR__ . '/../../includes/header.php';
?>
<div style="margin:0 auto;max-width:800px;padding:2rem" class="fade-in">
    <div class="page-header"><h1><i class="bi bi-person-circle text-cyan"></i> My Profile</h1><p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p></div>
    <?php if ($w): ?>
    <div class="row g-3">
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Personal</div><div class="card-body">
            <p><strong><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></strong></p>
            <p><label class="form-label">CNIC</label><?= htmlspecialchars($w['cnic']) ?></p>
            <p><label class="form-label">Phone</label><?= htmlspecialchars($w['phone']) ?></p>
            <p><label class="form-label">Company</label><?= htmlspecialchars($w['company_name']) ?></p>
        </div></div></div>
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Employment</div><div class="card-body">
            <p><label class="form-label">Designation</label><?= htmlspecialchars($w['designation']) ?></p>
            <p><label class="form-label">Department</label><?= htmlspecialchars($w['department'] ?? 'N/A') ?></p>
            <p><label class="form-label">Type</label><?= ucfirst(str_replace('_',' ',$w['employment_type'] ?? 'N/A')) ?></p>
            <p><label class="form-label">Status</label><span class="badge badge-<?= $w['is_active'] ? 'approved' : 'rejected' ?>"><?= $w['is_active'] ? 'Active' : 'Inactive' ?></span></p>
        </div></div></div>
    </div>
    <?php else: ?>
    <div class="alert alert-vms alert-warning">No worker profile found.</div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
