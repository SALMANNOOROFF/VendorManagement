<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Vendor.php';
require_once __DIR__ . '/../../../classes/FormConfig.php';

$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: ../dashboard.php'); exit; }

$fc = new FormConfig();
$allFields = $fc->getFields('worker_registration');

$groups = [];
foreach ($allFields as $field) {
    $groups[$field['field_group']][] = $field;
}

function renderWorkerCell(array $f, int $index): string {
    $name     = "workers[{$index}][" . htmlspecialchars($f['field_name']) . "]";
    $required = $f['is_mandatory'] ? 'required' : '';
    $ph       = htmlspecialchars($f['placeholder'] ?? '');

    switch ($f['field_type']) {
        case 'file':
            return "<td><input type=\"file\" name=\"{$name}\" class=\"form-control form-control-sm form-control-vms-glass\" {$required} accept=\".jpg,.jpeg,.png,.pdf\" style=\"min-width:150px\"></td>";
        case 'date':
            return "<td><input type=\"date\" name=\"{$name}\" class=\"form-control form-control-sm form-control-vms-glass\" {$required} style=\"min-width:130px\"></td>";
        case 'number':
            return "<td><input type=\"number\" name=\"{$name}\" class=\"form-control form-control-sm form-control-vms-glass\" {$required} min=\"0\" style=\"min-width:80px\"></td>";
        case 'select':
            return "<td><select name=\"{$name}\" class=\"form-select form-select-sm form-control-vms-glass\" {$required} style=\"min-width:120px\"><option value=\"\">--</option></select></td>";
        default:
            return "<td><input type=\"text\" name=\"{$name}\" class=\"form-control form-control-sm form-control-vms-glass\" {$required} placeholder=\"{$ph}\" style=\"min-width:150px\"></td>";
    }
}
?>

<?php
$pageTitle = 'Bulk Add Workers';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>

<style>
    .form-control-vms-glass {
        background: #ffffff !important;
        border: 1px solid rgba(0, 255, 255, 0.2) !important;
        color: #0d1f3c !important;
        border-radius: 6px !important;
        padding: 0.5rem 0.75rem !important;
        font-weight: 500 !important;
    }
    .form-control-vms-glass::placeholder {
        color: #6c757d !important;
        opacity: 0.7;
    }
    .form-control-vms-glass:focus {
        background: #ffffff !important;
        border-color: var(--cyan) !important;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.2) !important;
        outline: none !important;
    }
    .table-vms-bulk thead th {
        background: rgba(0, 255, 255, 0.1);
        color: var(--cyan);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid var(--cyan);
        padding: 15px 12px;
        white-space: nowrap;
    }
    .table-vms-bulk tbody td {
        vertical-align: middle;
        padding: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }
    .row-no {
        color: var(--text-light);
        font-weight: 600;
        width: 40px;
        text-align: center;
    }
</style>

<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-cyan p-2 rounded-3 me-3" style="--bs-bg-opacity: .1">
                <i class="bi bi-people-fill text-cyan fs-4"></i>
            </div>
            <div>
                <h1 class="h3 mb-0">Bulk Add Workers</h1>
                <p class="text-muted-vms mb-0">Enter details for multiple workers in a tabular format</p>
            </div>
        </div>
        <button type="button" class="btn btn-outline-cyan" onclick="addRow()"><i class="bi bi-plus-lg"></i> Add Another Row</button>
    </div>
    
    <div id="worker-alerts"></div>
    <form id="bulk-worker-form" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="vendor_id" value="<?= $vendor['id'] ?>">
        
        <div class="card card-vms-premium mb-4">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px">
                    <table class="table table-vms-bulk mb-0" id="workers-table">
                        <thead>
                            <tr>
                                <th class="text-center">S#</th>
                                <?php foreach ($allFields as $f): ?>
                                    <th><?= htmlspecialchars($f['field_label']) ?><?php if($f['is_mandatory']) echo ' <span class="text-cyan">*</span>'; ?></th>
                                <?php endforeach; ?>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="workers-body">
                            <!-- Rows will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-4 mb-5">
            <button type="submit" class="btn btn-cyan btn-lg px-5 fs-6 shadow-lg" id="submit-bulk">
                <i class="bi bi-check-lg"></i> Save All Workers
            </button>
            <a href="list.php" class="btn btn-outline-secondary btn-lg px-4 fs-6 border-0">Cancel</a>
        </div>
    </form>
</div>

<script>
let rowIndex = 0;
const fields = <?= json_encode($allFields) ?>;

function addRow() {
    const tbody = document.getElementById('workers-body');
    const row = document.createElement('tr');
    row.id = `row-${rowIndex}`;
    
    let cells = `<td class="row-no">${rowIndex + 1}</td>`;
    
    fields.forEach(f => {
        const name = `workers[${rowIndex}][${f.field_name}]`;
        const required = f.is_mandatory ? 'required' : '';
        const ph = f.placeholder || '';
        
        let input = '';
        if (f.field_type === 'file') {
            input = `<input type="file" name="${name}" class="form-control form-control-sm form-control-vms-glass" ${required} accept=".jpg,.jpeg,.png,.pdf" style="min-width:150px">`;
        } else if (f.field_type === 'date') {
            input = `<input type="date" name="${name}" class="form-control form-control-sm form-control-vms-glass" ${required} style="min-width:130px">`;
        } else if (f.field_type === 'number') {
            input = `<input type="number" name="${name}" class="form-control form-control-sm form-control-vms-glass" ${required} min="0" style="min-width:80px">`;
        } else if (f.field_type === 'select') {
            let options = '<option value="">--</option>';
            if (f.field_name === 'employment_type') {
                const types = ['Daily', 'Weekly', 'Monthly', 'Contract', 'Permanent', 'Daily Wage'];
                types.forEach(t => {
                    options += `<option value="${t.toLowerCase().replace(' ', '_')}">${t}</option>`;
                });
            }
            input = `<select name="${name}" class="form-select form-select-sm form-control-vms-glass" ${required} style="min-width:120px">${options}</select>`;
        } else {
            input = `<input type="text" name="${name}" class="form-control form-control-sm form-control-vms-glass" ${required} placeholder="${ph}" style="min-width:150px">`;
        }
        cells += `<td>${input}</td>`;
    });
    
    cells += `<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(${rowIndex})"><i class="bi bi-x-lg"></i></button></td>`;
    
    row.innerHTML = cells;
    tbody.appendChild(row);
    rowIndex++;
}

function removeRow(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (row) row.remove();
    // Re-index row numbers
    const rows = document.querySelectorAll('.row-no');
    rows.forEach((el, i) => el.innerText = i + 1);
}

// Add initial row
document.addEventListener('DOMContentLoaded', addRow);

document.getElementById('bulk-worker-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!this.checkValidity()) {
        this.reportValidity();
        return;
    }

    const fd = new FormData(this);
    const btn = document.getElementById('submit-bulk');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

    fetch(APP_URL + '/api/worker_add.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.success) { 
                showToast(res.message, 'success');
                setTimeout(() => window.location.href = 'list.php', 1000);
            } else { 
                showToast(res.message || 'Error', 'danger'); 
                btn.disabled = false; 
                btn.innerHTML = '<i class="bi bi-check-lg"></i> Save All Workers'; 
            }
        }).catch(() => { 
            showToast('Server error', 'danger'); 
            btn.disabled = false; 
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Save All Workers'; 
        });
});
</script>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
