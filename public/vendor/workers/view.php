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
foreach ($allFields as $field) {
    $groups[$field['field_group']][] = $field;
}

$pageTitle = 'View Worker';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<style>
    .info-label {
        color: var(--cyan);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        margin-bottom: 2px;
        display: block;
        opacity: 0.8;
    }
    .info-value {
        color: var(--white);
        font-size: 1rem;
        font-weight: 500;
        display: block;
    }
    .card-vms-premium {
        background: rgba(13, 31, 60, 0.4) !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 255, 255, 0.05) !important;
        border-radius: 15px !important;
        height: 100%;
    }
    .group-header {
        border-bottom: 1px solid rgba(0, 255, 255, 0.05);
        padding-bottom: 10px;
        margin-bottom: 15px;
        color: var(--text-light);
        font-weight: 600;
        display: flex;
        align-items: center;
    }
</style>

<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center mb-4 mt-2">
        <div class="d-flex align-items-center">
            <div class="bg-cyan p-3 rounded-4 me-3" style="--bs-bg-opacity: .1; border: 1px solid rgba(0,255,255,0.1)">
                <i class="bi bi-person-bounding-box text-cyan fs-3"></i>
            </div>
            <div>
                <h1 class="h2 mb-0 fw-bold"><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></h1>
                <p class="text-muted-vms mb-0">Worker Profile Detail • <?= htmlspecialchars($w['cnic']) ?></p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="list.php" class="btn btn-outline-secondary px-4 rounded-pill border-0"><i class="bi bi-arrow-left"></i> Back</a>
            <a href="edit.php?id=<?= $w['id'] ?>" class="btn btn-cyan px-4 rounded-pill shadow-lg"><i class="bi bi-pencil-square me-2"></i> Edit Profile</a>
        </div>
    </div>

    <div class="row g-4">
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
            <div class="card card-vms-premium">
                <div class="card-body p-4">
                    <div class="group-header">
                        <i class="<?= $info['icon'] ?> text-cyan me-2"></i> <?= $info['title'] ?>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($fields as $f): 
                            $val = $w[$f['field_name']] ?? 'N/A';
                            if ($f['field_type'] === 'file') {
                                $val = $val !== 'N/A' ? "<a href='" . APP_URL . "/assets/uploads/worker_docs/{$val}' target='_blank' class='btn btn-sm btn-outline-cyan mt-1 px-3 py-1'><i class='bi bi-eye'></i> View Doc</a>" : "<span class='text-muted small'>Not Uploaded</span>";
                            } else {
                                $val = htmlspecialchars($val);
                            }
                        ?>
                        <div class="col-sm-6 mb-2">
                            <span class="info-label"><?= htmlspecialchars($f['field_label']) ?></span>
                            <span class="info-value"><?= $val ?: '—' ?></span>
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
