<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../classes/Vendor.php';
$vendorModel = new Vendor();
$vendors = $vendorModel->getAll();
$pageTitle = 'All Vendors';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
require_once __DIR__ . '/../../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-building"></i> All Vendors</h1><p>Complete vendor registry</p></div>
    <div class="card card-vms">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-vms mb-0">
                <thead><tr><th>#</th><th>Company</th><th>Type</th><th>Contact</th><th>City</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($vendors as $i => $v): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($v['company_name']) ?></strong></td>
                    <td><?= htmlspecialchars($v['type_name']) ?></td>
                    <td><?= htmlspecialchars($v['primary_contact_name']) ?><br><small class="text-muted-vms"><?= htmlspecialchars($v['email']) ?></small></td>
                    <td><?= htmlspecialchars($v['city']) ?></td>
                    <td><span class="badge badge-<?= $v['verification_status'] === 'verified' ? 'approved' : ($v['verification_status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= ucfirst($v['verification_status']) ?></span></td>
                    <td class="text-muted-vms"><?= date('d M Y', strtotime($v['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($vendors)): ?><tr><td colspan="7" class="empty-state">No vendors registered yet</td></tr><?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
