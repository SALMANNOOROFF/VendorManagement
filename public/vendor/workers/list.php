<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Vendor.php';
require_once __DIR__ . '/../../../classes/Worker.php';
$vendorModel = new Vendor();
$workerModel = new Worker();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: ../dashboard.php'); exit; }
$workers = $workerModel->getByVendor($vendor['id']);
$pageTitle = 'Workers';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div><h1><i class="bi bi-people-fill text-cyan"></i> Workers</h1><p><?= count($workers) ?> registered worker(s)</p></div>
        <a href="add.php" class="btn btn-cyan"><i class="bi bi-person-plus"></i> Add Worker</a>
    </div>
    <div class="card card-vms">
        <div class="card-body p-0">
            <table class="table table-vms mb-0">
                <thead><tr><th>Name</th><th>CNIC</th><th>Designation</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($workers as $w): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></strong></td>
                    <td><?= htmlspecialchars($w['cnic']) ?></td>
                    <td><?= htmlspecialchars($w['designation']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $w['employment_type'] ?? 'N/A')) ?></td>
                    <td><span class="badge badge-<?= $w['is_active'] ? 'approved' : 'rejected' ?>"><?= $w['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    <td>
                        <a href="view.php?id=<?= $w['id'] ?>" class="btn btn-outline-cyan btn-sm"><i class="bi bi-eye"></i></a>
                        <a href="edit.php?id=<?= $w['id'] ?>" class="btn btn-outline-cyan btn-sm"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($workers)): ?><tr><td colspan="6" class="empty-state"><i class="bi bi-people"></i>No workers added yet. <a href="add.php">Add your first worker</a></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
