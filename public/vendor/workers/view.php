<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Worker.php';
require_once __DIR__ . '/../../../classes/Vendor.php';
require_once __DIR__ . '/../../../classes/FormConfig.php';

$workerModel = new Worker();
$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
$id = (int)($_GET['id'] ?? 0);
$w = $workerModel->getById($id);
if (!$w || !$vendor || $w['vendor_id'] != $vendor['id']) { header('Location: list.php'); exit; }

$fc = new FormConfig();
$allFields = $fc->getFields('worker_registration');
$groups = [];
foreach ($allFields as $field) { $groups[$field['field_group']][] = $field; }

$pageTitle = 'View Worker';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1><i class="bi bi-person-bounding-box"></i> <?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></h1>
            <p>Worker Profile • <?= htmlspecialchars($w['cnic']) ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="list.php" class="btn btn-outline-cyan"><i class="bi bi-arrow-left"></i> Back</a>
            <a href="edit.php?id=<?= $w['id'] ?>" class="btn btn-cyan"><i class="bi bi-pencil-square"></i> Edit</a>
        </div>
    </div>

    <div class="row g-3">
        <?php
        $groupInfo = [
            'account' => ['title' => 'Personal Information', 'icon' => 'bi-person'],
            'company_info' => ['title' => 'Personal Information', 'icon' => 'bi-person'],
            'contact' => ['title' => 'Contact Details', 'icon' => 'bi-telephone'],
            'address' => ['title' => 'Address Information', 'icon' => 'bi-geo-alt'],
            'banking' => ['title' => 'Banking Details', 'icon' => 'bi-bank'],
            'documents' => ['title' => 'Documents', 'icon' => 'bi-file-earmark-check'],
            'extra' => ['title' => 'Other Details', 'icon' => 'bi-info-circle']
        ];
        foreach ($groups as $grpKey => $fields):
            $info = $groupInfo[$grpKey] ?? ['title' => ucfirst($grpKey), 'icon' => 'bi-info-circle'];
        ?>
        <div class="col-md-6">
            <div class="card card-vms" style="height:100%">
                <div class="card-header"><i class="<?= $info['icon'] ?>"></i> <?= $info['title'] ?></div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($fields as $f):
                            $val = $w[$f['field_name']] ?? 'N/A';
                            if ($f['field_type'] === 'file') {
                                $val = ($val !== 'N/A' && $val) ? "<a href='" . ($appUrl ?? '/VendorM/public') . "/assets/uploads/worker_docs/{$val}' target='_blank' class='btn btn-sm btn-outline-cyan'><i class='bi bi-eye'></i> View Doc</a>" : "<span class='text-muted-vms'>Not Uploaded</span>";
                            } else {
                                $val = htmlspecialchars($val);
                            }
                        ?>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label"><?= htmlspecialchars($f['field_label']) ?></label>
                            <p style="font-weight:500;margin:0"><?= $val ?: '—' ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
