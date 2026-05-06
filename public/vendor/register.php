<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../classes/CompanyType.php';
require_once __DIR__ . '/../../classes/FormConfig.php';

$ct    = new CompanyType();
$types = $ct->getAll();
$fc    = new FormConfig();

// Get visible fields from DB, grouped
$allFields = $fc->getFields('vendor_registration');

// Separate file fields and regular fields by group
$groups  = [];
$files   = [];
$docKeys = ['registration_certificate','ntn_certificate','tax_certificate','bank_statement','company_profile_doc'];

foreach ($allFields as $field) {
    if ($field['field_type'] === 'file') {
        $files[] = $field;
    } else {
        $groups[$field['field_group']][] = $field;
    }
}

// Step mapping (group → step number and title)
$stepMap = [
    'account'      => ['step' => 1, 'title' => 'Account',  'icon' => 'bi-person-circle'],
    'company_info' => ['step' => 2, 'title' => 'Company',  'icon' => 'bi-building'],
    'contact'      => ['step' => 3, 'title' => 'Contact',  'icon' => 'bi-person-lines-fill'],
    'address'      => ['step' => 3, 'title' => 'Contact',  'icon' => 'bi-person-lines-fill'],
    'documents'    => ['step' => 4, 'title' => 'Documents','icon' => 'bi-folder2-open'],
];

$hideNav   = true;
$pageTitle = 'Vendor Registration';
require_once __DIR__ . '/../../includes/header.php';

// Helper: render one field
function renderField(array $f, array $types): string {
    $label    = htmlspecialchars($f['field_label']);
    $name     = htmlspecialchars($f['field_name']);
    $ph       = htmlspecialchars($f['placeholder'] ?? '');
    $required = $f['is_mandatory'] ? 'required' : '';
    $star     = $f['is_mandatory'] ? ' *' : '';
    $help     = $f['help_text'] ? '<div class="form-text text-muted-vms">' . htmlspecialchars($f['help_text']) . '</div>' : '';

    switch ($f['field_type']) {
        case 'email':
            return "<div class=\"col-md-6 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                    <input type=\"email\" name=\"{$name}\" class=\"form-control form-control-vms\" {$required} placeholder=\"{$ph}\">{$help}</div>";

        case 'number':
            return "<div class=\"col-md-4 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                    <input type=\"number\" name=\"{$name}\" class=\"form-control form-control-vms\" {$required} placeholder=\"{$ph}\" min=\"0\">{$help}</div>";

        case 'date':
            return "<div class=\"col-md-6 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                    <input type=\"date\" name=\"{$name}\" class=\"form-control form-control-vms\" {$required}>{$help}</div>";

        case 'textarea':
            return "<div class=\"col-12 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                    <textarea name=\"{$name}\" class=\"form-control form-control-vms\" rows=\"3\" {$required} placeholder=\"{$ph}\"></textarea>{$help}</div>";

        case 'select':
            // Special selects
            if ($name === 'company_type_id') {
                $opts = "<option value=\"\">-- Select Type --</option>";
                foreach ($types as $t) {
                    $opts .= "<option value=\"{$t['id']}\">" . htmlspecialchars($t['type_name']) . "</option>";
                }
                return "<div class=\"col-md-6 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                        <select name=\"{$name}\" id=\"company_type\" class=\"form-select form-control-vms\" {$required}>{$opts}</select>{$help}</div>";
            }
            if ($name === 'company_subtype_id') {
                return "<div class=\"col-md-6 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                        <select name=\"{$name}\" id=\"company_subtype\" class=\"form-select form-control-vms\"><option value=\"\">-- Select Type First --</option></select>{$help}</div>";
            }
            return "<div class=\"col-md-6 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                    <select name=\"{$name}\" class=\"form-select form-control-vms\" {$required}><option value=\"\">-- Select --</option></select>{$help}</div>";

        case 'file':
            return ""; // files handled separately

        default: // text and custom cf_* fields
            return "<div class=\"col-md-6 mb-3\"><label class=\"form-label\">{$label}{$star}</label>
                    <input type=\"text\" name=\"{$name}\" class=\"form-control form-control-vms\" {$required} placeholder=\"{$ph}\">{$help}</div>";
    }
}
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
        <div class="step" data-step="4"><span class="step-number">4</span><span class="step-title">Documents</span></div>
    </div>

    <form id="vendor-reg-form" enctype="multipart/form-data" novalidate>
    <div class="card card-vms">
    <div class="card-body" style="padding:2rem">

        <!-- ===== STEP 1: Account Credentials (always static) ===== -->
        <div class="form-step active" id="step-1">
            <h5 class="text-cyan mb-3"><i class="bi bi-person-circle"></i> Account Credentials</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control form-control-vms" required placeholder="Choose a username">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control form-control-vms" required placeholder="your@email.com">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control form-control-vms" required minlength="6" placeholder="Min 6 characters">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="password_confirm" class="form-control form-control-vms" required placeholder="Repeat password">
                </div>
            </div>
            <div class="text-end mt-3"><button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button></div>
        </div>

        <!-- ===== STEP 2: Company Info (dynamic from DB) ===== -->
        <div class="form-step" id="step-2">
            <h5 class="text-cyan mb-3"><i class="bi bi-building"></i> Company Information</h5>
            <div class="row">
                <?php
                $companyGroups = ['company_info'];
                foreach ($companyGroups as $grp) {
                    if (!empty($groups[$grp])) {
                        foreach ($groups[$grp] as $f) {
                            echo renderField($f, $types);
                        }
                    }
                }
                ?>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-outline-cyan btn-prev"><i class="bi bi-arrow-left"></i> Back</button>
                <button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- ===== STEP 3: Contact & Address (dynamic from DB) ===== -->
        <div class="form-step" id="step-3">
            <h5 class="text-cyan mb-3"><i class="bi bi-person-lines-fill"></i> Contact & Address</h5>
            <div class="row">
                <?php
                $contactGroups = ['contact'];
                foreach ($contactGroups as $grp) {
                    if (!empty($groups[$grp])) {
                        foreach ($groups[$grp] as $f) {
                            echo renderField($f, $types);
                        }
                    }
                }
                ?>
                <?php if (!empty($groups['address'])): ?>
                <div class="col-12"><hr style="border-color:var(--navy-mid)"><p class="text-muted-vms" style="font-size:0.8rem">ADDRESS</p></div>
                <?php foreach ($groups['address'] as $f): echo renderField($f, $types); endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-outline-cyan btn-prev"><i class="bi bi-arrow-left"></i> Back</button>
                <button type="button" class="btn btn-cyan btn-next">Next <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <!-- ===== STEP 4: Documents (dynamic from DB) ===== -->
        <div class="form-step" id="step-4">
            <h5 class="text-cyan mb-3"><i class="bi bi-folder2-open"></i> Document Upload</h5>
            <p class="text-muted-vms" style="font-size:0.85rem">Accepted: PDF, JPG, PNG — Max 5MB each</p>
            <?php if (empty($files)): ?>
                <div class="alert" style="background:var(--navy-mid);color:var(--text-light)">No document fields configured.</div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($files as $f):
                    $star     = $f['is_mandatory'] ? ' *' : '';
                    $required = $f['is_mandatory'] ? 'required' : '';
                ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?= htmlspecialchars($f['field_label']) ?><?= $star ?></label>
                    <input type="file" name="<?= htmlspecialchars($f['field_name']) ?>"
                        class="form-control form-control-vms" <?= $required ?>
                        accept=".pdf,.jpg,.jpeg,.png">
                    <?php if ($f['help_text']): ?>
                        <div class="form-text text-muted-vms"><?= htmlspecialchars($f['help_text']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
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

    if (!this.checkValidity()) {
        showToast('Form contains incomplete or invalid fields. Please review all steps.', 'danger');
        return;
    }

    // Validate passwords match
    const pw = $('input[name="password"]').val();
    const pw2 = $('input[name="password_confirm"]').val();
    if (pw !== pw2) {
        showToast('Passwords do not match', 'danger');
        return;
    }

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
