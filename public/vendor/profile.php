<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../classes/Vendor.php';
$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: dashboard.php'); exit; }
$pageTitle = 'Company Profile';
require_once __DIR__ . '/../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-building"></i> Company Profile</h1></div>
    <div class="row g-3">
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Company Identity</div><div class="card-body">
            <div class="mb-2"><label class="form-label">Company Name</label><p><?= htmlspecialchars($vendor['company_name']) ?></p></div>
            <div class="mb-2"><label class="form-label">Registration No</label><p><?= htmlspecialchars($vendor['company_registration_no'] ?? 'N/A') ?></p></div>
            <div class="mb-2"><label class="form-label">NTN</label><p><?= htmlspecialchars($vendor['ntn_number'] ?? 'N/A') ?></p></div>
            <div class="mb-2"><label class="form-label">Type</label><p><?= htmlspecialchars($vendor['type_name']) ?> <?= $vendor['subtype_name'] ? '/ ' . htmlspecialchars($vendor['subtype_name']) : '' ?></p></div>
            <div class="mb-2"><label class="form-label">Years in Business</label><p><?= $vendor['years_in_business'] ?? 'N/A' ?></p></div>
        </div></div></div>
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Contact & Address</div><div class="card-body">
            <div class="mb-2"><label class="form-label">Contact Person</label><p><?= htmlspecialchars($vendor['primary_contact_name']) ?></p></div>
            <div class="mb-2"><label class="form-label">Phone</label><p><?= htmlspecialchars($vendor['primary_contact_phone']) ?></p></div>
            <div class="mb-2"><label class="form-label">Email</label><p><?= htmlspecialchars($vendor['primary_contact_email']) ?></p></div>
            <div class="mb-2"><label class="form-label">Address</label><p><?= htmlspecialchars($vendor['address_line1']) ?>, <?= htmlspecialchars($vendor['city']) ?></p></div>
        </div></div></div>
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Banking</div><div class="card-body">
            <div class="mb-2"><label class="form-label">Bank</label><p><?= htmlspecialchars($vendor['bank_name'] ?? 'N/A') ?></p></div>
            <div class="mb-2"><label class="form-label">Account</label><p><?= htmlspecialchars($vendor['bank_account_no'] ?? 'N/A') ?></p></div>
            <div class="mb-2"><label class="form-label">IBAN</label><p><?= htmlspecialchars($vendor['iban'] ?? 'N/A') ?></p></div>
        </div></div></div>
        <div class="col-md-6"><div class="card card-vms"><div class="card-header">Status</div><div class="card-body">
            <div class="mb-2"><label class="form-label">Verification</label><p><span class="badge badge-<?= $vendor['verification_status'] === 'verified' ? 'approved' : 'pending' ?>"><?= ucfirst($vendor['verification_status']) ?></span></p></div>
            <div class="mb-2"><label class="form-label">Account</label><p><span class="badge badge-<?= $vendor['account_status'] === 'active' ? 'approved' : 'pending' ?>"><?= ucfirst($vendor['account_status']) ?></span></p></div>
        </div></div></div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
