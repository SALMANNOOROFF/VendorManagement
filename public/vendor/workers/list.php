<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Vendor.php';
require_once __DIR__ . '/../../../classes/Worker.php';
$vendorModel = new Vendor();
$workerModel = new Worker();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: ../dashboard.php'); exit; }
$workers = $workerModel->getByVendor($vendor['id']);
$pageTitle = 'Workers';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1><i class="bi bi-people-fill"></i> Workforce Management</h1>
            <p><?= count($workers) ?> total registered worker(s)</p>
        </div>
        <a href="add.php" class="btn btn-cyan"><i class="bi bi-person-plus-fill"></i> Add Worker</a>
    </div>

    <div class="card card-vms mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text" style="background:var(--surface-dim);border-color:var(--outline);color:var(--primary)">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="workerSearch" class="form-control form-control-vms" placeholder="Search by Name, CNIC, Phone, or Address...">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <select id="typeFilter" class="form-select form-control-vms">
                            <option value="">All Employment Types</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="contract">Contract</option>
                            <option value="permanent">Permanent</option>
                            <option value="daily_wage">Daily Wage</option>
                        </select>
                        <button class="btn btn-outline-cyan" onclick="resetFilters()"><i class="bi bi-arrow-counterclockwise"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-vms">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-vms mb-0" id="workerTable">
                    <thead>
                        <tr>
                            <th>Full Name</th><th>CNIC / Phone</th><th>Designation</th><th>Address</th><th>Type</th><th>Status</th><th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($workers as $w):
                        $searchStr = strtolower($w['first_name'] . ' ' . $w['last_name'] . ' ' . $w['cnic'] . ' ' . ($w['phone']??'') . ' ' . ($w['address']??''));
                        $type = strtolower($w['employment_type'] ?? '');
                    ?>
                    <tr class="worker-row" data-search="<?= htmlspecialchars($searchStr) ?>" data-type="<?= $type ?>">
                        <td>
                            <strong><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></strong><br>
                            <small class="text-muted-vms"><?= htmlspecialchars($w['department'] ?? 'No Dept') ?></small>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($w['cnic']) ?></div>
                            <small style="color:var(--primary)"><?= htmlspecialchars($w['phone'] ?? 'No Phone') ?></small>
                        </td>
                        <td><?= htmlspecialchars($w['designation']) ?></td>
                        <td><div class="text-truncate" style="max-width:200px" title="<?= htmlspecialchars($w['address'] ?? '') ?>"><?= htmlspecialchars($w['address'] ?? 'N/A') ?></div></td>
                        <td><span class="badge badge-active"><?= ucfirst(str_replace('_', ' ', $w['employment_type'] ?? 'N/A')) ?></span></td>
                        <td><span class="badge badge-<?= $w['is_active'] ? 'approved' : 'rejected' ?>"><?= $w['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                        <td class="text-center">
                            <a href="view.php?id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-cyan" title="View"><i class="bi bi-eye"></i></a>
                            <a href="edit.php?id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-cyan" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($workers)): ?>
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-people"></i>No workers found. <a href="add.php">Add your first worker</a></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function filterWorkers() {
    const query = document.getElementById('workerSearch').value.toLowerCase();
    const type = document.getElementById('typeFilter').value.toLowerCase();
    document.querySelectorAll('.worker-row').forEach(row => {
        const matchesSearch = row.getAttribute('data-search').includes(query);
        const matchesType = type === '' || row.getAttribute('data-type') === type;
        row.style.display = (matchesSearch && matchesType) ? '' : 'none';
    });
}
function resetFilters() {
    document.getElementById('workerSearch').value = '';
    document.getElementById('typeFilter').value = '';
    filterWorkers();
}
document.getElementById('workerSearch').addEventListener('input', filterWorkers);
document.getElementById('typeFilter').addEventListener('change', filterWorkers);
</script>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
