<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['approver','super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
$v = new Vendor();
$approved = $v->getAll(['verification_status' => 'verified']);
$rejected = $v->getAll(['verification_status' => 'rejected']);
$pageTitle = 'Review History';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar_approver.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-clock-history text-cyan"></i> Review History</h1></div>
    <ul class="nav nav-tabs mb-3" style="border-color:var(--navy-mid)">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#approved-tab" style="color:var(--success);background:var(--navy-mid);border-color:var(--navy-mid)">Approved (<?= count($approved) ?>)</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#rejected-tab" style="color:var(--danger);border-color:var(--navy-mid)">Rejected (<?= count($rejected) ?>)</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="approved-tab">
            <div class="card card-vms"><div class="card-body p-0">
                <table class="table table-vms mb-0">
                    <thead><tr><th>Company</th><th>Type</th><th>Contact</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($approved as $a): ?>
                    <tr><td><?= htmlspecialchars($a['company_name']) ?></td><td><?= htmlspecialchars($a['type_name']) ?></td><td><?= htmlspecialchars($a['primary_contact_name']) ?></td><td class="text-muted-vms"><?= date('d M Y', strtotime($a['created_at'])) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($approved)): ?><tr><td colspan="4" class="empty-state">No approved vendors</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
        <div class="tab-pane fade" id="rejected-tab">
            <div class="card card-vms"><div class="card-body p-0">
                <table class="table table-vms mb-0">
                    <thead><tr><th>Company</th><th>Reason</th><th>Date</th></tr></thead>
                    <tbody>
                    <?php foreach ($rejected as $r): ?>
                    <tr><td><?= htmlspecialchars($r['company_name']) ?></td><td class="text-muted-vms"><?= htmlspecialchars($r['rejection_reason'] ?? 'N/A') ?></td><td class="text-muted-vms"><?= date('d M Y', strtotime($r['created_at'])) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($rejected)): ?><tr><td colspan="3" class="empty-state">No rejected vendors</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
