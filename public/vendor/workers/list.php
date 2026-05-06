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
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
?>
<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-people-fill text-cyan"></i> Workforce Management</h1>
            <p class="text-muted-vms mb-0"><?= count($workers) ?> total registered worker(s)</p>
        </div>
        <a href="add.php" class="btn btn-cyan px-4 rounded-pill shadow-sm"><i class="bi bi-person-plus-fill me-2"></i> Add Worker</a>
    </div>

    <!-- Filters & Search -->
    <div class="card card-vms-premium mb-4 border-0" style="background: rgba(13, 31, 60, 0.6); backdrop-filter: blur(10px); border: 1px solid rgba(0, 255, 255, 0.1) !important;">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-dark border-0 text-cyan" style="border-radius: 10px 0 0 10px;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="workerSearch" class="form-control bg-dark text-white border-0 py-2" 
                               style="border-radius: 0 10px 10px 0;" 
                               placeholder="Search by Name, CNIC, Phone, or Address...">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="d-flex gap-2">
                        <select id="typeFilter" class="form-select bg-dark text-white border-0 py-2" style="border-radius: 10px;">
                            <option value="" class="bg-dark">All Employment Types</option>
                            <option value="daily" class="bg-dark">Daily</option>
                            <option value="weekly" class="bg-dark">Weekly</option>
                            <option value="monthly" class="bg-dark">Monthly</option>
                            <option value="contract" class="bg-dark">Contract</option>
                            <option value="permanent" class="bg-dark">Permanent</option>
                            <option value="daily_wage" class="bg-dark">Daily Wage</option>
                        </select>
                        <button class="btn btn-outline-cyan border-0" onclick="resetFilters()"><i class="bi bi-arrow-counterclockwise"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-vms-premium border-0 shadow-lg" style="border-radius: 15px; overflow: hidden; background: rgba(13, 31, 60, 0.3);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-vms mb-0 align-middle" id="workerTable">
                    <thead>
                        <tr style="background: rgba(0, 255, 255, 0.05);">
                            <th class="ps-4">Full Name</th>
                            <th>CNIC / Phone</th>
                            <th>Designation</th>
                            <th>Address</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($workers as $w): 
                        $searchStr = strtolower($w['first_name'] . ' ' . $w['last_name'] . ' ' . $w['cnic'] . ' ' . ($w['phone']??'') . ' ' . ($w['address']??''));
                        $type = strtolower($w['employment_type'] ?? '');
                    ?>
                    <tr class="worker-row" data-search="<?= htmlspecialchars($searchStr) ?>" data-type="<?= $type ?>">
                        <td class="ps-4">
                            <div class="fw-bold text-white"><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></div>
                            <small class="text-muted-vms"><?= htmlspecialchars($w['department'] ?? 'No Dept') ?></small>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($w['cnic']) ?></div>
                            <small class="text-cyan opacity-75"><?= htmlspecialchars($w['phone'] ?? 'No Phone') ?></small>
                        </td>
                        <td><?= htmlspecialchars($w['designation']) ?></td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($w['address'] ?? '') ?>">
                                <?= htmlspecialchars($w['address'] ?? 'N/A') ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3" style="background: rgba(0, 255, 255, 0.1); color: var(--cyan); border: 1px solid rgba(0, 255, 255, 0.2);">
                                <?= ucfirst(str_replace('_', ' ', $w['employment_type'] ?? 'N/A')) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $w['is_active'] ? 'approved' : 'rejected' ?>">
                                <?= $w['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="btn-group">
                                <a href="view.php?id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-cyan border-0" title="View"><i class="bi bi-eye"></i></a>
                                <a href="edit.php?id=<?= $w['id'] ?>" class="btn btn-sm btn-outline-cyan border-0 mx-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($workers)): ?>
                    <tr>
                        <td colspan="7" class="py-5 text-center text-muted-vms">
                            <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                            No workers found. <a href="add.php" class="text-cyan">Add your first worker</a>
                        </td>
                    </tr>
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
    const rows = document.querySelectorAll('.worker-row');

    rows.forEach(row => {
        const searchData = row.getAttribute('data-search');
        const typeData = row.getAttribute('data-type');
        
        const matchesSearch = searchData.includes(query);
        const matchesType = type === '' || typeData === type;

        if (matchesSearch && matchesType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
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
