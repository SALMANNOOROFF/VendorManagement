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

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => trim($_POST['first_name'] ?? $w['first_name']),
        'last_name' => trim($_POST['last_name'] ?? $w['last_name']),
        'phone' => trim($_POST['phone'] ?? $w['phone']),
        'designation' => trim($_POST['designation'] ?? $w['designation']),
        'department' => trim($_POST['department'] ?? ''),
        'employment_type' => $_POST['employment_type'] ?? $w['employment_type'],
    ];
    $workerModel->update($id, $data);
    $success = 'Worker updated.';
    $w = $workerModel->getById($id);
}

$pageTitle = 'Edit Worker';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-pencil text-cyan"></i> Edit Worker</h1></div>
    <?php if ($success): ?><div class="alert alert-vms alert-success"><?= $success ?></div><?php endif; ?>
    <div class="card card-vms" style="max-width:700px">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control form-control-vms" value="<?= htmlspecialchars($w['first_name']) ?>" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control form-control-vms" value="<?= htmlspecialchars($w['last_name']) ?>" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">CNIC</label><input type="text" class="form-control form-control-vms" value="<?= htmlspecialchars($w['cnic']) ?>" disabled></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Phone *</label><input type="text" name="phone" class="form-control form-control-vms" value="<?= htmlspecialchars($w['phone']) ?>" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Designation *</label><input type="text" name="designation" class="form-control form-control-vms" value="<?= htmlspecialchars($w['designation']) ?>" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control form-control-vms" value="<?= htmlspecialchars($w['department'] ?? '') ?>"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Type</label><select name="employment_type" class="form-select form-control-vms">
                        <?php foreach (['permanent','contract','temporary','daily_wage'] as $t): ?><option value="<?= $t ?>" <?= ($w['employment_type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$t)) ?></option><?php endforeach; ?>
                    </select></div>
                </div>
                <button type="submit" class="btn btn-cyan"><i class="bi bi-check-lg"></i> Save</button>
                <a href="list.php" class="btn btn-outline-cyan ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
