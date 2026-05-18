<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Vendor.php';
require_once __DIR__ . '/../../../classes/EntryRequest.php';
$vendorModel = new Vendor();
$entryModel = new EntryRequest();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: ../dashboard.php'); exit; }
$requests = $entryModel->getByVendor($vendor['id']);
$pageTitle = 'Entry Requests';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div><h1><i class="bi bi-shield-check"></i> Entry Requests</h1><p><?= count($requests) ?> total request(s)</p></div>
        <a href="add.php" class="btn btn-cyan"><i class="bi bi-plus-lg"></i> New Entry Request</a>
    </div>
    <div class="card card-vms"><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-vms mb-0">
            <thead><tr><th>Request ID</th><th>Date</th><th>Work Site</th><th>Type</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
            <tr>
                <td>#REQ-<?= str_pad($r['id'], 5, '0', STR_PAD_LEFT) ?></td>
                <td><?= date('d M Y, h:i A', strtotime($r['created_at'])) ?></td>
                <td><?= htmlspecialchars($r['place_of_work'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($r['type_of_worker'] ?? 'N/A') ?></td>
                <td>
                    <?php 
                        $statusStyle = '';
                        if ($r['status'] === 'approved') {
                            $statusStyle = 'background-color: rgba(16, 185, 129, 0.15); color: #047857;'; // Emerald
                        } elseif ($r['status'] === 'rejected') {
                            $statusStyle = 'background-color: rgba(239, 68, 68, 0.15); color: #b91c1c;'; // Red
                        } elseif ($r['status'] === 'pending') {
                            $statusStyle = 'background-color: rgba(245, 158, 11, 0.15); color: #b45309;'; // Amber
                        } else {
                            $statusStyle = 'background-color: rgba(100, 116, 139, 0.15); color: #334155;'; // Slate (Draft)
                        }
                    ?>
                    <span class="badge rounded-pill" style="<?= $statusStyle ?> font-weight:600; padding: 0.5em 0.8em;">
                        <?= ucfirst($r['status']) ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-2 justify-content-center align-items-center">
                        <a href="view.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-cyan rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="View Details">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if ($r['status'] === 'approved'): ?>
                            <a href="document.php?id=<?= $r['id'] ?>" target="_blank" class="btn btn-sm btn-emerald rounded-pill px-3 d-flex align-items-center gap-1" style="background-color: var(--emerald); color: white; border: none; font-size: 0.85rem; height: 32px;">
                                <i class="bi bi-printer"></i> <span>Gate Pass</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($r['status'] === 'draft' || $r['status'] === 'pending'): ?>
                            <a href="add.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-cyan rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit Request">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($requests)): ?>
            <tr><td colspan="6" class="empty-state"><i class="bi bi-shield-slash"></i>No entry requests. <a href="add.php">Create first</a></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div></div></div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
