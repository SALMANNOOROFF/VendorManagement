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

$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    foreach ($allFields as $f) {
        $name = $f['field_name'];
        if ($f['field_type'] !== 'file') {
            $data[$name] = trim($_POST[$name] ?? $w[$name] ?? '');
        }
    }
    if ($workerModel->update($id, $data)) {
        $success = 'Worker details updated successfully.';
        $w = $workerModel->getById($id);
    } else {
        $error = 'Failed to update worker details.';
    }
}

function renderEditField(array $f, $value): string {
    $label    = htmlspecialchars($f['field_label']);
    $name     = htmlspecialchars($f['field_name']);
    $required = $f['is_mandatory'] ? 'required' : '';
    $star     = $f['is_mandatory'] ? '<span style="color:var(--primary)">*</span>' : '';
    $val      = htmlspecialchars($value ?? '');
    if ($f['field_type'] === 'file') return "";
    $col = ($f['field_type'] === 'textarea') ? 'col-md-12' : 'col-md-6';
    $input = "";
    switch ($f['field_type']) {
        case 'date':
            $input = "<input type=\"date\" name=\"{$name}\" class=\"form-control form-control-vms\" value=\"{$val}\" {$required}>";
            break;
        case 'number':
            $input = "<input type=\"number\" name=\"{$name}\" class=\"form-control form-control-vms\" value=\"{$val}\" {$required}>";
            break;
        case 'textarea':
            $input = "<textarea name=\"{$name}\" class=\"form-control form-control-vms\" rows=\"3\" {$required}>{$val}</textarea>";
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
            $input = "<select name=\"{$name}\" class=\"form-select form-control-vms\" {$required}>{$options}</select>";
            break;
        default:
            $readonly = ($name === 'cnic') ? 'readonly' : '';
            $input = "<input type=\"text\" name=\"{$name}\" class=\"form-control form-control-vms\" value=\"{$val}\" {$required} {$readonly}>";
    }
    return "<div class=\"{$col} mb-3\"><label class=\"form-label\">{$label} {$star}</label>{$input}</div>";
}

$pageTitle = 'Edit Worker';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../../includes/header.php';
?>
<div class="main-content fade-in">
    <div style="max-width:900px;margin:0 auto">
        <div class="page-header mb-4">
            <h1><i class="bi bi-pencil-square"></i> Edit Worker</h1>
            <p>Update information for <?= htmlspecialchars($w['first_name']) ?></p>
        </div>
        <?php if ($success): ?><div class="alert alert-vms alert-success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-vms alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="card card-vms">
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($allFields as $f) { echo renderEditField($f, $w[$f['field_name']] ?? ''); } ?>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-cyan"><i class="bi bi-save"></i> Save Changes</button>
                <a href="list.php" class="btn btn-outline-cyan">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
