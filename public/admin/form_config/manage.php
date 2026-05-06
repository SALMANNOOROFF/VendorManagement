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
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-sliders text-cyan"></i> Form Configuration</h1>
            <p>Toggle mandatory/visible fields or add new ones</p>
        </div>
        <button class="btn btn-cyan" data-bs-toggle="modal" data-bs-target="#addFieldModal"><i class="bi bi-plus-lg"></i> Add Field</button>
    </div>

    <ul class="nav nav-tabs mb-3" style="border-color:var(--navy-mid)">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#vendor-tab" style="color:var(--cyan);background:var(--navy-mid);border-color:var(--navy-mid)">Vendor Registration</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#worker-tab" style="color:var(--text-light);border-color:var(--navy-mid)">Worker Registration</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="vendor-tab">
            <div class="card card-vms"><div class="card-body p-0">
                <table class="table table-vms mb-0">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Group</th><th class="text-center">Mandatory</th><th class="text-center">Visible</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($vendorFields as $f): ?>
                    <tr>
                        <td><code style="color:var(--cyan)"><?= $f['field_name'] ?></code></td>
                        <td>
                            <input type="text" class="form-control form-control-sm bg-transparent text-white border-0 edit-label" 
                                   data-form="vendor_registration" data-field="<?= $f['field_name'] ?>" 
                                   value="<?= htmlspecialchars($f['field_label']) ?>" style="min-width:150px">
                        </td>
                        <td><span class="badge" style="background:var(--navy-mid)"><?= $f['field_type'] ?></span></td>
                        <td class="text-muted-vms"><?= $f['field_group'] ?></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="vendor_registration" data-field="<?= $f['field_name'] ?>" data-key="mandatory" <?= $f['is_mandatory'] ? 'checked' : '' ?>></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="vendor_registration" data-field="<?= $f['field_name'] ?>" data-key="visible" <?= $f['is_visible'] ? 'checked' : '' ?>></td>
                        <td><button class="btn btn-sm btn-outline-danger delete-field" data-form="vendor_registration" data-field="<?= $f['field_name'] ?>"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
        <div class="tab-pane fade" id="worker-tab">
            <div class="card card-vms"><div class="card-body p-0">
                <table class="table table-vms mb-0">
                    <thead><tr><th>Field</th><th>Label</th><th>Type</th><th>Group</th><th class="text-center">Mandatory</th><th class="text-center">Visible</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($workerFields as $f): ?>
                    <tr>
                        <td><code style="color:var(--cyan)"><?= $f['field_name'] ?></code></td>
                        <td>
                            <input type="text" class="form-control form-control-sm bg-transparent text-white border-0 edit-label" 
                                   data-form="worker_registration" data-field="<?= $f['field_name'] ?>" 
                                   value="<?= htmlspecialchars($f['field_label']) ?>" style="min-width:150px">
                        </td>
                        <td><span class="badge" style="background:var(--navy-mid)"><?= $f['field_type'] ?></span></td>
                        <td class="text-muted-vms"><?= $f['field_group'] ?></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="worker_registration" data-field="<?= $f['field_name'] ?>" data-key="mandatory" <?= $f['is_mandatory'] ? 'checked' : '' ?>></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input toggle-field" data-form="worker_registration" data-field="<?= $f['field_name'] ?>" data-key="visible" <?= $f['is_visible'] ? 'checked' : '' ?>></td>
                        <td><button class="btn btn-sm btn-outline-danger delete-field" data-form="worker_registration" data-field="<?= $f['field_name'] ?>"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>

<!-- Add Field Modal -->
<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content bg-navy border-cyan">
        <form id="add-field-form">
            <div class="modal-header border-navy-mid"><h5 class="modal-title text-cyan">Add New Field</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Form Type</label><select name="form_type" class="form-select bg-navy text-white border-navy-mid"><option value="vendor_registration">Vendor Registration</option><option value="worker_registration">Worker Registration</option></select></div>
                <div class="mb-3"><label class="form-label">Field Name (DB Column)</label><input type="text" name="field_name" class="form-control bg-navy text-white border-navy-mid" placeholder="e.g. tax_id" required></div>
                <div class="mb-3"><label class="form-label">Display Label</label><input type="text" name="field_label" class="form-control bg-navy text-white border-navy-mid" placeholder="e.g. Tax ID Number" required></div>
                <div class="mb-3"><label class="form-label">Field Type</label><select name="field_type" class="form-select bg-navy text-white border-navy-mid"><option value="text">Text</option><option value="number">Number</option><option value="email">Email</option><option value="file">File</option><option value="textarea">Textarea</option></select></div>
                <div class="mb-3"><label class="form-label">Step/Group</label><select name="field_group" class="form-select bg-navy text-white border-navy-mid">
                    <option value="account">Account</option>
                    <option value="company_info">Company Info</option>
                    <option value="contact">Contact Details</option>
                    <option value="address">Address</option>
                    <option value="banking">Banking</option>
                    <option value="documents">Documents</option>
                    <option value="extra">Extra Info</option>
                </select></div>
            </div>
            <div class="modal-footer border-navy-mid"><button type="submit" class="btn btn-cyan">Save Field</button></div>
        </form>
    </div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Label change
    $('.edit-label').on('change', function() {
        const $el = $(this);
        $.post('/VendorM/public/api/update_field.php', {
            form_type: $el.data('form'),
            field_name: $el.data('field'),
            field_label: $el.val()
        }, function(res) {
            if(res.success) showToast('Label updated', 'success');
        });
    });

    // Add field
    $('#add-field-form').on('submit', function(e) {
        e.preventDefault();
        $.post('/VendorM/public/api/add_field.php', $(this).serialize(), function(res) {
            if(res.success) location.reload();
            else alert(res.message);
        });
    });

    // Delete field
    $('.delete-field').on('click', function() {
        if(!confirm('Are you sure?')) return;
        const $el = $(this);
        $.post('/VendorM/public/api/delete_field.php', {
            form_type: $el.data('form'),
            field_name: $el.data('field')
        }, function(res) {
            if(res.success) $el.closest('tr').remove();
        });
    });
});
</script>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
