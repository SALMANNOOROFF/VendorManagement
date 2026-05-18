<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['approver','super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
$v = new Vendor();
$pending = $v->getAll(['verification_status' => 'pending']);
$pageTitle = 'Pending Vendors';
require_once __DIR__ . '/../../includes/sidebar_approver.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-hourglass-split"></i> Pending Vendors</h1><p><?= count($pending) ?> vendor(s) awaiting review</p></div>
    <div class="card card-vms">
        <div class="card-body p-0"><div class="table-responsive">
            <table class="table table-vms mb-0">
                <thead><tr><th>Company</th><th>Type</th><th>Contact</th><th>Phone</th><th>Submitted</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($pending as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['company_name']) ?></strong></td>
                    <td><?= htmlspecialchars($p['type_name']) ?></td>
                    <td><?= htmlspecialchars($p['primary_contact_name']) ?></td>
                    <td><?= htmlspecialchars($p['primary_contact_phone']) ?></td>
                    <td class="text-muted-vms"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                    <td><a href="review.php?id=<?= $p['id'] ?>" class="btn btn-cyan btn-sm"><i class="bi bi-eye"></i> Review</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($pending)): ?><tr><td colspan="6" class="empty-state"><i class="bi bi-check-circle"></i>All clear! No pending vendors.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
