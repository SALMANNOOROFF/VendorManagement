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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    foreach ($allFields as $f) {
        $name = $f['field_name'];
        if ($f['field_type'] !== 'file') {
            $data[$name] = trim($_POST[$name] ?? $w[$name] ?? '');
        }
    }
    // Handle specific fields if needed
    if ($workerModel->update($id, $data)) {
        $success = 'Worker details updated successfully.';
        $w = $workerModel->getById($id);
    } else {
        $error = 'Failed to update worker details.';
    }
}

function renderEditField(array $f, $value): string {
    $label    = strtoupper(htmlspecialchars($f['field_label']));
    $name     = htmlspecialchars($f['field_name']);
    $required = $f['is_mandatory'] ? 'required' : '';
    $star     = $f['is_mandatory'] ? '<span class="text-cyan">*</span>' : '';
    $val      = htmlspecialchars($value ?? '');

    // Skip files in basic edit for now, or handle differently
    if ($f['field_type'] === 'file') return "";

    $col = ($f['field_type'] === 'textarea') ? 'col-md-12' : 'col-md-6';
    $input = "";
    
    switch ($f['field_type']) {
        case 'date':
            $input = "<input type=\"date\" name=\"{$name}\" class=\"form-control form-control-vms-glass\" value=\"{$val}\" {$required}>";
            break;
        case 'number':
            $input = "<input type=\"number\" name=\"{$name}\" class=\"form-control form-control-vms-glass\" value=\"{$val}\" {$required}>";
            break;
        case 'textarea':
            $input = "<textarea name=\"{$name}\" class=\"form-control form-control-vms-glass\" rows=\"3\" {$required}>{$val}</textarea>";
            break;
        case 'select':
            $options = '<option value="">-- Select --</option>';
            if ($name === 'employment_type') {
                $types = ['Daily', 'Weekly', 'Monthly', 'Contract', 'Permanent', 'Daily Wage'];
                foreach ($types as $t) {
                    $key = strtolower(str_replace(' ', '_', $t));
                    $sel = ($val === $key) ? 'selected' : '';
                    $options .= "<option value=\"{$key}\" {$sel}>{$t}</option>";
                }
            }
            $input = "<select name=\"{$name}\" class=\"form-select form-control-vms-glass\" {$required}>{$options}</select>";
            break;
        default:
            $readonly = ($name === 'cnic') ? 'readonly' : ''; // CNIC usually shouldn't be edited
            $input = "<input type=\"text\" name=\"{$name}\" class=\"form-control form-control-vms-glass\" value=\"{$val}\" {$required} {$readonly}>";
    }

    return "<div class=\"{$col} mb-4\">
                <label class=\"form-label fw-bold small text-muted-vms mb-1\" style=\"letter-spacing:1px\">{$label} {$star}</label>
                {$input}
            </div>";
}

$pageTitle = 'Edit Worker';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<style>
    .form-control-vms-glass {
        background: #ffffff !important;
        border: 1px solid rgba(0, 255, 255, 0.2) !important;
        color: #0d1f3c !important;
        padding: 0.75rem 1rem !important;
        border-radius: 10px !important;
        font-weight: 500 !important;
    }
    .form-control-vms-glass:focus {
        background: #ffffff !important;
        border-color: var(--cyan) !important;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.2) !important;
    }
    .card-vms-premium {
        background: rgba(13, 31, 60, 0.6) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0, 255, 255, 0.1) !important;
        border-radius: 16px !important;
    }
</style>

<div class="main-content fade-in">
    <div style="max-width:900px; margin: 0 auto">
        <div class="page-header d-flex align-items-center mb-4 mt-2">
            <div class="bg-cyan p-3 rounded-4 me-3" style="--bs-bg-opacity: .1; border: 1px solid rgba(0,255,255,0.1)">
                <i class="bi bi-pencil-square text-cyan fs-3"></i>
            </div>
            <div>
                <h1 class="h2 mb-0 fw-bold">Edit Worker</h1>
                <p class="text-muted-vms mb-0">Update information for <?= htmlspecialchars($w['first_name']) ?></p>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success border-0 shadow-sm mb-4" style="background: rgba(25, 135, 84, 0.2); color: #75b798;">
                <i class="bi bi-check-circle me-2"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="background: rgba(220, 53, 69, 0.2); color: #ea868f;">
                <i class="bi bi-exclamation-triangle me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="card card-vms-premium">
                <div class="card-body p-4">
                    <div class="row">
                        <?php 
                        foreach ($allFields as $f) {
                            echo renderEditField($f, $w[$f['field_name']] ?? '');
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 mt-4">
                <button type="submit" class="btn btn-cyan btn-lg px-5 fs-6 fw-bold shadow-lg rounded-pill">
                    <i class="bi bi-save me-2"></i> Save Changes
                </button>
                <a href="list.php" class="btn btn-link text-muted-vms text-decoration-none px-4 fw-medium">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
