<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/CompanyType.php';
require_once __DIR__ . '/../../classes/AuditLog.php';

$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if ($vendor) { header('Location: dashboard.php'); exit; }

$ct = new CompanyType();
$types = $ct->getAll();
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['company_name','company_type_id','primary_contact_name','primary_contact_phone','primary_contact_email','address_line1','city'];
    $valid = true;
    foreach ($required as $f) {
        if (empty(trim($_POST[$f] ?? ''))) { $error = "Field '$f' is required."; $valid = false; break; }
    }
    if ($valid) {
        try {
            $vendorId = $vendorModel->register($_SESSION['user_id'], $_POST);
            $vendorModel->createWorkflow($_SESSION['user_id']);
            $audit = new AuditLog();
            $audit->log($_SESSION['user_id'], 'vendor_profile_completed', 'vendor', $vendorId);
            header('Location: dashboard.php');
            exit;
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Complete Profile';
require_once __DIR__ . '/../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-building"></i> Complete Your Company Profile</h1><p>Fill in your company details to activate your vendor account</p></div>
    <?php if ($error): ?><div class="alert alert-vms alert-danger"><?= $error ?></div><?php endif; ?>

    <form method="POST">
    <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-building"></i> Company Information</div><div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Company Name *</label><input type="text" name="company_name" class="form-control form-control-vms" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Registration No</label><input type="text" name="company_registration_no" class="form-control form-control-vms"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Company Type *</label>
                <select name="company_type_id" id="company_type" class="form-select form-control-vms" required>
                    <option value="">-- Select Type --</option>
                    <?php foreach ($types as $t): ?><option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['type_name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3"><label class="form-label">Sub-Type</label><select name="company_subtype_id" id="company_subtype" class="form-select form-control-vms"><option value="">-- Select Type First --</option></select></div>
            <div class="col-md-4 mb-3"><label class="form-label">NTN Number</label><input type="text" name="ntn_number" class="form-control form-control-vms"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Years in Business</label><input type="number" name="years_in_business" class="form-control form-control-vms" min="0"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Employees</label><input type="number" name="number_of_employees" class="form-control form-control-vms" min="1"></div>
            <div class="col-12 mb-3"><label class="form-label">Business Description</label><textarea name="business_description" class="form-control form-control-vms" rows="3"></textarea></div>
        </div>
    </div></div>

    <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-person-lines-fill"></i> Contact & Address</div><div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Primary Contact *</label><input type="text" name="primary_contact_name" class="form-control form-control-vms" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Phone *</label><input type="text" name="primary_contact_phone" class="form-control form-control-vms" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Email *</label><input type="email" name="primary_contact_email" class="form-control form-control-vms" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">CNIC</label><input type="text" name="primary_contact_cnic" class="form-control form-control-vms"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Address *</label><input type="text" name="address_line1" class="form-control form-control-vms" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Address Line 2</label><input type="text" name="address_line2" class="form-control form-control-vms"></div>
            <div class="col-md-4 mb-3"><label class="form-label">City *</label><input type="text" name="city" class="form-control form-control-vms" required></div>
            <div class="col-md-4 mb-3"><label class="form-label">State/Province</label><input type="text" name="state_province" class="form-control form-control-vms"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Country</label><input type="text" name="country" class="form-control form-control-vms" value="Pakistan"></div>
        </div>
    </div></div>

    <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-bank2"></i> Banking Information</div><div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control form-control-vms"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Account Title</label><input type="text" name="bank_account_title" class="form-control form-control-vms"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Account No</label><input type="text" name="bank_account_no" class="form-control form-control-vms"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Branch</label><input type="text" name="bank_branch" class="form-control form-control-vms"></div>
            <div class="col-md-6 mb-3"><label class="form-label">IBAN</label><input type="text" name="iban" class="form-control form-control-vms"></div>
        </div>
    </div></div>

    <button type="submit" class="btn btn-cyan"><i class="bi bi-check-lg"></i> Save Profile</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
