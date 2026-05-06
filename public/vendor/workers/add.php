<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Vendor.php';
$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: ../dashboard.php'); exit; }
$pageTitle = 'Add Worker';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-person-plus-fill text-cyan"></i> Add Worker</h1></div>
    <div id="worker-alerts"></div>
    <form id="add-worker-form" enctype="multipart/form-data">
        <input type="hidden" name="vendor_id" value="<?= $vendor['id'] ?>">
        <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-person"></i> Personal Information</div><div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control form-control-vms" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control form-control-vms" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">CNIC *</label><input type="text" name="cnic" class="form-control form-control-vms" required placeholder="XXXXX-XXXXXXX-X"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control form-control-vms"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select form-control-vms"><option value="">--</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                <div class="col-md-4 mb-3"><label class="form-label">Phone *</label><input type="text" name="phone" class="form-control form-control-vms" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control form-control-vms"></div>
            </div>
        </div></div>
        <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-briefcase"></i> Employment</div><div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Designation *</label><input type="text" name="designation" class="form-control form-control-vms" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Department</label><input type="text" name="department" class="form-control form-control-vms"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Join Date *</label><input type="date" name="join_date" class="form-control form-control-vms" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Type</label><select name="employment_type" class="form-select form-control-vms"><option value="">--</option><option value="permanent">Permanent</option><option value="contract">Contract</option><option value="temporary">Temporary</option><option value="daily_wage">Daily Wage</option></select></div>
                <div class="col-md-4 mb-3"><label class="form-label">Salary (PKR)</label><input type="number" name="monthly_salary" class="form-control form-control-vms" min="0"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Experience (Years)</label><input type="number" name="experience_years" class="form-control form-control-vms" min="0" value="0"></div>
            </div>
        </div></div>
        <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-upload"></i> Documents</div><div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">CNIC Front *</label><input type="file" name="cnic_front" class="form-control form-control-vms" required accept=".jpg,.jpeg,.png,.pdf"></div>
                <div class="col-md-6 mb-3"><label class="form-label">CNIC Back</label><input type="file" name="cnic_back" class="form-control form-control-vms" accept=".jpg,.jpeg,.png,.pdf"></div>
            </div>
        </div></div>
        <button type="submit" class="btn btn-cyan" id="submit-worker"><i class="bi bi-check-lg"></i> Add Worker</button>
        <a href="list.php" class="btn btn-outline-cyan ms-2">Cancel</a>
    </form>
</div>
<script>
document.getElementById('add-worker-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const btn = document.getElementById('submit-worker');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
    fetch(APP_URL + '/api/worker_add.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.success) { window.location.href = 'list.php'; }
            else { showToast(res.message || 'Error', 'danger'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Add Worker'; }
        }).catch(() => { showToast('Server error', 'danger'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Add Worker'; });
});
</script>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
