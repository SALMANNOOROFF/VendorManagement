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

    $colClass = 'col-md-6 mb-2';
    if ($f['field_type'] === 'textarea') $colClass = 'col-12 mb-2';
    if (preg_match('/(company name|registration|company type)/i', $label)) $colClass = 'col-md-4 mb-2';
    if (isset($f['force_col'])) $colClass = $f['force_col'] . ' mb-2';

    switch ($f['field_type']) {
        case 'email':
            return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                    <input type=\"email\" name=\"{$name}\" class=\"form-control form-control-vms form-control-sm\" {$required} placeholder=\"{$ph}\">{$help}</div>";

        case 'number':
            return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                    <input type=\"number\" name=\"{$name}\" class=\"form-control form-control-vms form-control-sm\" {$required} placeholder=\"{$ph}\" min=\"0\">{$help}</div>";

        case 'date':
            return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                    <input type=\"date\" name=\"{$name}\" class=\"form-control form-control-vms form-control-sm\" {$required}>{$help}</div>";

        case 'textarea':
            return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                    <textarea name=\"{$name}\" class=\"form-control form-control-vms form-control-sm\" rows=\"2\" {$required} placeholder=\"{$ph}\"></textarea>{$help}</div>";

        case 'select':
            // Special selects
            if ($name === 'company_type_id') {
                $opts = "<option value=\"\">-- Select Type --</option>";
                foreach ($types as $t) {
                    $opts .= "<option value=\"{$t['id']}\">" . htmlspecialchars($t['type_name']) . "</option>";
                }
                return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                        <select name=\"{$name}\" id=\"company_type\" class=\"form-select form-control-vms form-control-sm\" {$required}>{$opts}</select>{$help}</div>";
            }
            if ($name === 'company_subtype_id') {
                return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                        <select name=\"{$name}\" id=\"company_subtype\" class=\"form-select form-control-vms form-control-sm\"><option value=\"\">-- Select Type First --</option></select>{$help}</div>";
            }
            return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                    <select name=\"{$name}\" class=\"form-select form-control-vms form-control-sm\" {$required}><option value=\"\">-- Select --</option></select>{$help}</div>";

        case 'file':
            return ""; // files handled separately

        default: // text and custom cf_* fields
            return "<div class=\"{$colClass}\"><label class=\"form-label\" style=\"font-size:0.65rem\">{$label}{$star}</label>
                    <input type=\"text\" name=\"{$name}\" class=\"form-control form-control-vms form-control-sm\" {$required} placeholder=\"{$ph}\">{$help}</div>";
    }
}
?>
<div style="min-height:100vh;background:var(--surface);padding:0.5rem 0">
<div class="container-fluid px-4 px-lg-5" style="max-width:1400px">
    <div class="text-center mb-4 fade-in">
        <a href="/VendorM/public/" style="text-decoration:none"><i class="bi bi-shield-check" style="font-size:2.4rem;color:var(--primary-light)"></i> <span style="color:var(--primary-light);font-weight:700;font-size:1.4rem">VMS</span></a>
        <h2 style="color:var(--on-surface);margin-top:0.5rem;font-weight:700">Vendor Registration</h2>
        <p style="color:var(--on-surface-variant);font-size:1rem">Complete all steps to submit your application</p>
    </div>

    <div id="reg-alerts"></div>

    <!-- Step Indicator (Modern Animated Pill Design) -->
    <div class="steps-progress-wrapper d-flex justify-content-center align-items-center" style="margin-bottom: 3rem; gap: 0;">
        <div class="step active" data-step="1">
            <div class="step-pill">
                <i class="bi bi-person-badge"></i>
                <span>Account</span>
            </div>
        </div>
        
        <div class="step-progress-line">
            <div class="progress-fill" id="progress-line-fill"></div>
        </div>
        
        <div class="step" data-step="2">
            <div class="step-pill">
                <i class="bi bi-building-check"></i>
                <span>Company Info</span>
            </div>
        </div>
    </div>

    <form id="vendor-reg-form" enctype="multipart/form-data" novalidate>
        
        <!-- ===== STEP 1: Account Credentials (always static) ===== -->
        <div class="form-step active" id="step-1">
            <div class="card card-vms mx-auto" style="max-width: 600px;">
                <div class="card-body" style="padding:1.5rem">
                    <h6 style="color:var(--primary)" class="mb-3"><i class="bi bi-person-circle"></i> Account Credentials</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label" style="font-size:0.65rem">Username *</label>
                    <input type="text" name="username" class="form-control form-control-vms form-control-sm" required placeholder="Choose a username">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" style="font-size:0.65rem">Email *</label>
                    <input type="email" name="email" class="form-control form-control-vms form-control-sm" required placeholder="your@email.com">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" style="font-size:0.65rem">Password *</label>
                    <input type="password" name="password" class="form-control form-control-vms form-control-sm" required minlength="6" placeholder="Min 6 characters">
                    <div class="form-text text-muted-vms mt-1" style="font-size: 0.65rem; color: #94a3b8 !important;">
                        <i class="bi bi-info-circle text-primary"></i> Password must be at least 6 characters long.
                    </div>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" style="font-size:0.65rem">Confirm Password *</label>
                    <input type="password" name="password_confirm" class="form-control form-control-vms form-control-sm" required placeholder="Repeat password">
                </div>
            </div>
                    <div class="text-end mt-3"><button type="button" class="btn btn-cyan btn-sm btn-next px-4">Next <i class="bi bi-arrow-right"></i></button></div>
                </div>
            </div>
        </div>

        <!-- ===== STEP 2: Details & Documents (dynamic from DB) ===== -->
        <div class="form-step" id="step-2">
            <div class="card card-vms mx-auto" style="max-width: 1400px;">
                <div class="card-body" style="padding:1.5rem">
                    <div class="row g-4">
                <!-- Left Column: Company & Address -->
                <div class="col-lg-6" style="border-right: 1px solid var(--outline-variant);">
                    <div class="mb-4">
                        <h6 style="color:var(--primary)" class="mb-3 border-bottom pb-2"><i class="bi bi-building"></i> Company Information</h6>
                        <div class="row">
                            <?php
                            if (!empty($groups['company_info'])) {
                                foreach ($groups['company_info'] as $f) {
                                    echo renderField($f, $types);
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 style="color:var(--primary)" class="mb-3 border-bottom pb-2"><i class="bi bi-geo-alt"></i> Address Details</h6>
                        <div class="row">
                            <?php 
                            if (!empty($groups['address'])) {
                                foreach ($groups['address'] as $f) {
                                    echo renderField($f, $types); 
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Contact & Documents -->
                <div class="col-lg-6">
                    <div class="mb-4">
                        <h6 style="color:var(--primary)" class="mb-3 border-bottom pb-2"><i class="bi bi-person-lines-fill"></i> Contact Details</h6>
                        <div class="row">
                            <?php
                            $primaryContact = [];
                            $secondaryContact = [];
                            if (!empty($groups['contact'])) {
                                foreach ($groups['contact'] as $f) {
                                    $n = strtolower($f['field_name'] . ' ' . $f['field_label']);
                                    if (strpos($n, 'secondary') !== false || strpos($n, 'sec ') !== false) {
                                        $secondaryContact[] = $f;
                                    } else {
                                        $primaryContact[] = $f;
                                    }
                                }
                            }
                            ?>
                            <div class="col-md-6 pe-md-3">
                                <div class="row">
                                    <?php 
                                    foreach ($primaryContact as $f) {
                                        $f['force_col'] = 'col-12';
                                        echo renderField($f, $types);
                                    } 
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6 ps-md-3" style="border-left: 1px solid var(--outline-variant);">
                                <div class="row">
                                    <?php 
                                    foreach ($secondaryContact as $f) {
                                        $f['force_col'] = 'col-12';
                                        echo renderField($f, $types);
                                    } 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 style="color:var(--primary)" class="mb-3 border-bottom pb-2"><i class="bi bi-folder2-open"></i> Document Upload</h6>
                        <p style="color:var(--gray-mid);font-size:0.75rem;margin-bottom:0.75rem">PDF, JPG, PNG — Max 5MB each</p>
                        <?php if (empty($files)): ?>
                            <div class="alert p-2" style="background:var(--surface-dim);color:var(--gray-mid);font-size:0.8rem">No document fields configured.</div>
                        <?php else: ?>
                        <div class="row">
                            <?php foreach ($files as $f):
                                $star     = $f['is_mandatory'] ? ' *' : '';
                                $required = $f['is_mandatory'] ? 'required' : '';
                            ?>
                            <div class="col-md-6 mb-2">
                                <label class="form-label" style="font-size:0.65rem"><?= htmlspecialchars($f['field_label']) ?><?= $star ?></label>
                                <input type="file" name="<?= htmlspecialchars($f['field_name']) ?>"
                                    class="form-control form-control-vms form-control-sm" <?= $required ?>
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <?php if ($f['help_text']): ?>
                                    <div class="form-text text-muted-vms" style="font-size:0.65rem"><?= htmlspecialchars($f['help_text']) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

                    <div class="d-flex justify-content-between mt-4 border-top pt-3" style="border-color:var(--outline-variant)!important">
                        <button type="button" class="btn btn-outline-cyan btn-sm btn-prev px-4"><i class="bi bi-arrow-left"></i> Back</button>
                        <button type="submit" class="btn btn-cyan btn-sm px-4" id="submit-btn"><i class="bi bi-send"></i> Submit Registration</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
</div>

<script src="/VendorM/assets/js/jquery.min.js"></script>
<script src="/VendorM/assets/js/bootstrap.bundle.min.js"></script>
<script src="/VendorM/assets/js/main.js"></script>
<script>
$('#vendor-reg-form').on('submit', function(e) {
    e.preventDefault();

    let valid = true;
    let firstInvalidStep = null;

    $(this).find('input, select, textarea').each(function() {
        if (typeof this.checkValidity === 'function' && !this.checkValidity()) {
            $(this).addClass('is-invalid').addClass('is-invalid-shake');
            const $el = $(this);
            setTimeout(function() { $el.removeClass('is-invalid-shake'); }, 500);
            valid = false;
            
            if (firstInvalidStep === null) {
                const $stepDiv = $(this).closest('.form-step');
                if ($stepDiv.length) {
                    const stepId = $stepDiv.attr('id');
                    if (stepId) {
                        firstInvalidStep = parseInt(stepId.replace('step-', ''));
                    }
                }
            }
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Validate passwords match
    const pw = $('input[name="password"]').val();
    const pw2 = $('input[name="password_confirm"]').val();
    if (pw && pw2 && pw !== pw2) {
        $('input[name="password"]').addClass('is-invalid').addClass('is-invalid-shake');
        $('input[name="password_confirm"]').addClass('is-invalid').addClass('is-invalid-shake');
        const $pwInputs = $('input[name="password"], input[name="password_confirm"]');
        setTimeout(function() { $pwInputs.removeClass('is-invalid-shake'); }, 500);
        
        valid = false;
        if (firstInvalidStep === null) firstInvalidStep = 1;
        showToast('Passwords do not match', 'danger');
    }

    if (!valid) {
        showToast('Form contains incomplete or invalid fields. Please review steps marked in red.', 'danger');
        if (firstInvalidStep !== null) {
            // Smoothly navigate the user back to the step with the first error!
            $('.form-step').removeClass('active');
            $('#step-' + firstInvalidStep).addClass('active');
            
            $('.step').removeClass('active completed');
            if (firstInvalidStep === 1) {
                $('.step[data-step="1"]').addClass('active');
            } else {
                $('.step[data-step="1"]').addClass('completed');
                $('.step[data-step="2"]').addClass('active');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        return;
    }

    // Real-time error removal when correcting
    $('#vendor-reg-form').on('input change', 'input, select, textarea', function() {
        if (typeof this.checkValidity === 'function' && this.checkValidity()) {
            $(this).removeClass('is-invalid');
        }
    });

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
