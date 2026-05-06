<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../classes/Vendor.php';
$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: dashboard.php'); exit; }
$pageTitle = 'Documents';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar_vendor.php';

$docs = [
    'Registration Certificate' => $vendor['registration_certificate'],
    'NTN Certificate' => $vendor['ntn_certificate'],
    'Tax Certificate' => $vendor['tax_certificate'],
    'Bank Statement' => $vendor['bank_statement'],
    'Company Profile' => $vendor['company_profile_doc'],
];
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-folder2-open text-cyan"></i> Documents</h1></div>
    <div class="row g-3">
        <?php foreach ($docs as $label => $path): ?>
        <div class="col-md-6">
            <div class="card card-vms">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><i class="bi bi-file-earmark-text text-cyan" style="font-size:1.5rem"></i> <strong><?= $label ?></strong></div>
                    <?php if ($path): ?>
                    <span class="badge badge-approved">Uploaded</span>
                    <?php else: ?>
                    <span class="badge badge-pending">Not Uploaded</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
