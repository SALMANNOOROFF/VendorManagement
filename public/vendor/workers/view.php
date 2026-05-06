<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Worker.php';
require_once __DIR__ . '/../../../classes/Vendor.php';
$workerModel = new Worker();
$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
$id = (int)($_GET['id'] ?? 0);
$w = $workerModel->getById($id);
if (!$w || !$vendor || $w['vendor_id'] != $vendor['id']) { header('Location: list.php'); exit; }
$pageTitle = 'View Worker';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-person text-cyan"></i> <?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></h1></div>
    <div class="row g-3">
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Personal</div><div class="card-body">
            <p><label class="form-label">CNIC</label><?= htmlspecialchars($w['cnic']) ?></p>
            <p><label class="form-label">Phone</label><?= htmlspecialchars($w['phone']) ?></p>
            <p><label class="form-label">Gender</label><?= ucfirst($w['gender'] ?? 'N/A') ?></p>
            <p><label class="form-label">DOB</label><?= $w['date_of_birth'] ?? 'N/A' ?></p>
        </div></div></div>
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Employment</div><div class="card-body">
            <p><label class="form-label">Designation</label><?= htmlspecialchars($w['designation']) ?></p>
            <p><label class="form-label">Department</label><?= htmlspecialchars($w['department'] ?? 'N/A') ?></p>
            <p><label class="form-label">Type</label><?= ucfirst(str_replace('_',' ',$w['employment_type'] ?? 'N/A')) ?></p>
            <p><label class="form-label">Joined</label><?= $w['join_date'] ?></p>
            <p><label class="form-label">Status</label><span class="badge badge-<?= $w['is_active'] ? 'approved' : 'rejected' ?>"><?= $w['is_active'] ? 'Active' : 'Inactive' ?></span></p>
        </div></div></div>
    </div>
    <div class="mt-3"><a href="list.php" class="btn btn-outline-cyan"><i class="bi bi-arrow-left"></i> Back</a> <a href="edit.php?id=<?= $w['id'] ?>" class="btn btn-cyan"><i class="bi bi-pencil"></i> Edit</a></div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
