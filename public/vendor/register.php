<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/CompanyType.php';

$ct = new CompanyType();
$types = $ct->getAll();
$hideNav = true;
$pageTitle = 'Vendor Registration';
require_once __DIR__ . '/../../includes/header.php';
?>
<div style="min-height:100vh;background:linear-gradient(135deg,var(--navy),#0d1f3c);padding:2rem 0">
<div class="container" style="max-width:900px">
    <div class="text-center mb-4 fade-in">
        <a href="/VendorM/public/" style="text-decoration:none"><i class="bi bi-shield-check" style="font-size:2rem;color:var(--cyan)"></i> <span style="color:var(--cyan);font-weight:700;font-size:1.2rem">VMS</span></a>
        <h2 style="color:var(--white);margin-top:1rem">Vendor Registration</h2>
        <p class="text-muted-vms">Complete all steps to submit your application</p>
    </div>

    <div id="reg-alerts"></div>

    <!-- Step Indicator -->
    <div class="steps-indicator">
        <div class="step active" data-step="1"><span class="step-number">1</span><span class="step-title">Account</span></div>
        <div class="step-divider"></div>
        <div class="step" data-step="2"><span class="step-number">2</span><span class="step-title">Company</span></div>
        <div class="step-divider"></div>
        <div class="step" data-step="3"><span class="step-number">3</span><span class="step-title">Contact</span></div>
        <div class="step-divider"></div>
        <div class="step" data-step="4"><span class="step-number">4</span><span class="step-title">Banking</span></div>
        <div class="step-divider"></div>
        <div class="step" data-step="5"><span class="step-number">5</span><span class="step-title">Documents</span></div>
    </div>

    <form id="vendor-reg-form" enctype="multipart/form-data">
    <div class="card card-vms">
    <div class="card-body" style="padding:2rem">

        <!-- Step 1: Account -->
        <div class="form-step active" id="step-1">
            <h5 class="text-cyan mb-3"><i class="bi bi-person-circle"></i> Account Credentials</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Username *</label><input type="text" name="username" class="form-control form-control-vms" required placeholder="Choose a username"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control form-control-vms" required placeholder="your@email.com"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control form-control-vms" required minlength="6" placeholder="Min 6 characters"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Confirm Password *</label><input type="password" name="password_confirm" class="form-control form-control-vms" required placeholder="Repeat password"></div>
            </div>
            <div class="text-end mt-3"><button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button></div>
        </div>

        <!-- Step 2: Company Info -->
        <div class="form-step" id="step-2">
            <h5 class="text-cyan mb-3"><i class="bi bi-building"></i> Company Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Company Name *</label><input type="text" name="company_name" class="form-control form-control-vms" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Registration No *</label><input type="text" name="company_registration_no" class="form-control form-control-vms" required></div>
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
            <div class="d-flex justify-content-between mt-3"><button type="button" class="btn btn-outline-cyan btn-prev"><i class="bi bi-arrow-left"></i> Back</button><button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button></div>
        </div>

        <!-- Step 3: Contact & Address -->
        <div class="form-step" id="step-3">
            <h5 class="text-cyan mb-3"><i class="bi bi-person-lines-fill"></i> Contact & Address</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Primary Contact Name *</label><input type="text" name="primary_contact_name" class="form-control form-control-vms" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Phone *</label><input type="text" name="primary_contact_phone" class="form-control form-control-vms" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Contact Email *</label><input type="email" name="primary_contact_email" class="form-control form-control-vms" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">CNIC</label><input type="text" name="primary_contact_cnic" class="form-control form-control-vms"></div>
                <div class="col-12"><hr style="border-color:var(--navy-mid)"><p class="text-muted-vms" style="font-size:0.8rem">ADDRESS</p></div>
                <div class="col-md-6 mb-3"><label class="form-label">Address Line 1 *</label><input type="text" name="address_line1" class="form-control form-control-vms" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Address Line 2</label><input type="text" name="address_line2" class="form-control form-control-vms"></div>
                <div class="col-md-4 mb-3"><label class="form-label">City *</label><input type="text" name="city" class="form-control form-control-vms" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">State/Province</label><input type="text" name="state_province" class="form-control form-control-vms"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Country</label><input type="text" name="country" class="form-control form-control-vms" value="Pakistan"></div>
            </div>
            <div class="d-flex justify-content-between mt-3"><button type="button" class="btn btn-outline-cyan btn-prev"><i class="bi bi-arrow-left"></i> Back</button><button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button></div>
        </div>

        <!-- Step 4: Banking -->
        <div class="form-step" id="step-4">
            <h5 class="text-cyan mb-3"><i class="bi bi-bank2"></i> Banking Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control form-control-vms"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Account Title</label><input type="text" name="bank_account_title" class="form-control form-control-vms"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Account No</label><input type="text" name="bank_account_no" class="form-control form-control-vms"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Branch</label><input type="text" name="bank_branch" class="form-control form-control-vms"></div>
                <div class="col-md-6 mb-3"><label class="form-label">IBAN</label><input type="text" name="iban" class="form-control form-control-vms"></div>
            </div>
            <div class="d-flex justify-content-between mt-3"><button type="button" class="btn btn-outline-cyan btn-prev"><i class="bi bi-arrow-left"></i> Back</button><button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button></div>
        </div>

        <!-- Step 5: Documents -->
        <div class="form-step" id="step-5">
            <h5 class="text-cyan mb-3"><i class="bi bi-folder2-open"></i> Document Upload</h5>
            <p class="text-muted-vms" style="font-size:0.85rem">Accepted: PDF, JPG, PNG — Max 5MB each</p>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Registration Certificate *</label><input type="file" name="registration_certificate" class="form-control form-control-vms" required accept=".pdf,.jpg,.jpeg,.png"></div>
                <div class="col-md-6 mb-3"><label class="form-label">NTN Certificate</label><input type="file" name="ntn_certificate" class="form-control form-control-vms" accept=".pdf,.jpg,.jpeg,.png"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Tax Certificate</label><input type="file" name="tax_certificate" class="form-control form-control-vms" accept=".pdf,.jpg,.jpeg,.png"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Bank Statement</label><input type="file" name="bank_statement" class="form-control form-control-vms" accept=".pdf,.jpg,.jpeg,.png"></div>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-outline-cyan btn-prev"><i class="bi bi-arrow-left"></i> Back</button>
                <button type="submit" class="btn btn-cyan" id="submit-btn"><i class="bi bi-send"></i> Submit Registration</button>
            </div>
        </div>

    </div>
    </div>
    </form>
</div>
</div>

<script src="/VendorM/public/assets/js/jquery.min.js"></script>
<script src="/VendorM/public/assets/js/bootstrap.bundle.min.js"></script>

<script src="/VendorM/public/assets/js/main.js"></script>
<script>
$('#vendor-reg-form').on('submit', function(e) {
    e.preventDefault();
    // Validate passwords match
    const pw = $('input[name="password"]').val();
    const pw2 = $('input[name="password_confirm"]').val();
    if (pw !== pw2) { showToast('Passwords do not match', 'danger'); return; }

    const fd = new FormData(this);
    $('#submit-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

    $.ajax({
        url: APP_URL + '/api/vendor_register.php',
        type: 'POST', data: fd, processData: false, contentType: false, dataType: 'json',
        success: function(res) {
            if (res.success) {
                window.location.href = APP_URL + '/login.php?registered=1';
            } else {
                showToast(res.message || 'Registration failed', 'danger');
                $('#submit-btn').prop('disabled', false).html('<i class="bi bi-send"></i> Submit Registration');
            }
        },
        error: function() {
            showToast('Server error', 'danger');
            $('#submit-btn').prop('disabled', false).html('<i class="bi bi-send"></i> Submit Registration');
        }
    });
});
</script>
</body></html>
