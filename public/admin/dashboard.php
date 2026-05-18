<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/Worker.php';
require_once __DIR__ . '/../../classes/User.php';

$vendor = new Vendor();
$worker = new Worker();
$user = new User();

$stats = [
    'pending'  => $vendor->count(['verification_status' => 'pending']),
    'verified' => $vendor->count(['verification_status' => 'verified']),
    'rejected' => $vendor->count(['verification_status' => 'rejected']),
    'workers'  => $worker->countAll(['is_active' => 1]),
    'users'    => $user->count(['status' => 'active']),
];
$recentVendors = $vendor->getAll();
$recentVendors = array_slice($recentVendors, 0, 10);

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../../includes/sidebar_admin.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header">
        <h1><i class="bi bi-grid-1x2-fill"></i> Dashboard</h1>
        <p>System overview and recent activity</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6"><div class="stat-card stat-warning"><div class="stat-number"><?= $stats['pending'] ?></div><div class="stat-label">Pending Vendors</div></div></div>
        <div class="col-md-3 col-6"><div class="stat-card stat-success"><div class="stat-number"><?= $stats['verified'] ?></div><div class="stat-label">Approved Vendors</div></div></div>
        <div class="col-md-3 col-6"><div class="stat-card stat-danger"><div class="stat-number"><?= $stats['rejected'] ?></div><div class="stat-label">Rejected</div></div></div>
        <div class="col-md-3 col-6"><div class="stat-card"><div class="stat-number"><?= $stats['workers'] ?></div><div class="stat-label">Active Workers</div></div></div>
    </div>

    <div class="card card-vms">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-building"></i> Recent Vendors</span>
            <a href="vendors/list.php" class="btn btn-outline-cyan btn-sm">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-vms mb-0">
                <thead><tr><th>Company</th><th>Type</th><th>Contact</th><th>Status</th><th>Registered</th></tr></thead>
                <tbody>
                <?php foreach ($recentVendors as $v): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($v['company_name']) ?></strong></td>
                    <td><?= htmlspecialchars($v['type_name']) ?></td>
                    <td><?= htmlspecialchars($v['primary_contact_name']) ?></td>
                    <td><span class="badge badge-<?= $v['verification_status'] === 'verified' ? 'approved' : ($v['verification_status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= ucfirst($v['verification_status']) ?></span></td>
                    <td class="text-muted-vms"><?= date('d M Y', strtotime($v['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentVendors)): ?>
                <tr><td colspan="5" class="empty-state"><i class="bi bi-inbox"></i>No vendors yet</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
