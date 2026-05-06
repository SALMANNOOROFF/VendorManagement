<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../classes/FormConfig.php';
$fc = new FormConfig();
$vendorFields = $fc->getAllFields('vendor_registration');
$workerFields = $fc->getAllFields('worker_registration');
$pageTitle = 'Form Configuration';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-sliders text-cyan"></i> Form Configuration</h1><p>Toggle mandatory/visible fields</p></div>
    <ul class="nav nav-tabs mb-3" style="border-color:var(--navy-mid)">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#vendor-tab" style="color:var(--cyan);background:var(--navy-mid);border-color:var(--navy-mid)">Vendor Registration</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#worker-tab" style="color:var(--text-light);border-color:var(--navy-mid)">Worker Registration</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="vendor-tab">
            <div class="card card-vms"><div class="card-body p-0">
                <table class="table table-vms mb-0">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Group</th><th class="text-center">Mandatory</th><th class="text-center">Visible</th></tr></thead>
                    <tbody>
                    <?php foreach ($vendorFields as $f): ?>
                    <tr>
                        <td><code style="color:var(--cyan)"><?= $f['field_name'] ?></code></td>
                        <td><?= htmlspecialchars($f['field_label']) ?></td>
                        <td><span class="badge" style="background:var(--navy-mid)"><?= $f['field_type'] ?></span></td>
                        <td class="text-muted-vms"><?= $f['field_group'] ?></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="vendor_registration" data-field="<?= $f['field_name'] ?>" data-key="mandatory" <?= $f['is_mandatory'] ? 'checked' : '' ?>></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="vendor_registration" data-field="<?= $f['field_name'] ?>" data-key="visible" <?= $f['is_visible'] ? 'checked' : '' ?>></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
        <div class="tab-pane fade" id="worker-tab">
            <div class="card card-vms"><div class="card-body p-0">
                <table class="table table-vms mb-0">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Group</th><th class="text-center">Mandatory</th><th class="text-center">Visible</th></tr></thead>
                    <tbody>
                    <?php foreach ($workerFields as $f): ?>
                    <tr>
                        <td><code style="color:var(--cyan)"><?= $f['field_name'] ?></code></td>
                        <td><?= htmlspecialchars($f['field_label']) ?></td>
                        <td><span class="badge" style="background:var(--navy-mid)"><?= $f['field_type'] ?></span></td>
                        <td class="text-muted-vms"><?= $f['field_group'] ?></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="worker_registration" data-field="<?= $f['field_name'] ?>" data-key="mandatory" <?= $f['is_mandatory'] ? 'checked' : '' ?>></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="worker_registration" data-field="<?= $f['field_name'] ?>" data-key="visible" <?= $f['is_visible'] ? 'checked' : '' ?>></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
