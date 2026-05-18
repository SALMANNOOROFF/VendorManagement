<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../../../classes/Vendor.php';
require_once __DIR__ . '/../../../classes/Worker.php';
require_once __DIR__ . '/../../../classes/EntryRequest.php';

$vendorModel = new Vendor();
$workerModel = new Worker();
$entryModel = new EntryRequest();

$vendor = $vendorModel->getByUserId($_SESSION['user_id']);
if (!$vendor) { header('Location: ../dashboard.php'); exit; }

$allWorkers = $workerModel->getByVendor($vendor['id']);
$requestId = $_GET['id'] ?? null;
$requestData = null;

if ($requestId) {
    $requestData = $entryModel->getById($requestId);
    if (!$requestData || $requestData['vendor_id'] != $vendor['id']) {
        header('Location: list.php');
        exit;
    }
}


$pageTitle = $requestId ? 'Edit Entry Request' : 'New Entry Request';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="main-content fade-in">
    <div class="page-header mb-4 text-center">
        <h2 style="color:var(--primary)" class="mb-1"><?= htmlspecialchars($vendor['company_name']) ?></h2>
        <h5 style="color:var(--gray-mid)"><?= $requestId ? 'Edit Entry Permission Request' : 'New Entry Permission Request' ?></h5>
    </div>

    <form method="POST" id="entryForm">
        <div class="row g-4">
            <!-- Left Side: Request Details -->
            <div class="col-lg-4">
                <div class="card card-vms" style="height:100%">
                    <div class="card-header">Request Information</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted-vms">Type of Worker</label>
                            <input type="text" name="type_of_worker" class="form-control form-control-vms" 
                                   placeholder="e.g. MENTINGS WORK Block E4, E5" required 
                                   value="<?= htmlspecialchars($requestData['type_of_worker'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted-vms">Place of Work / Site</label>
                            <input type="text" name="place_of_work" class="form-control form-control-vms" 
                                   placeholder="e.g. Block E Type E4, E5" required
                                   value="<?= htmlspecialchars($requestData['place_of_work'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted-vms">Main Vehicle No (Optional)</label>
                            <input type="text" name="main_vehicle_no" class="form-control form-control-vms" 
                                   placeholder="e.g. KS 6490"
                                   value="<?= htmlspecialchars($requestData['vehicle_no'] ?? '') ?>">
                        </div>
                        <div class="alert alert-vms alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Save as Draft if you want to edit later. Use "Send for Permission" to submit for approval.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Worker Selection -->
            <div class="col-lg-8">
                <div class="card card-vms">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span><i class="bi bi-people"></i> Select Workers</span>
                        <div style="position:relative;width:250px">
                            <input type="text" id="workerSearch" class="form-control form-control-sm form-control-vms" placeholder="Search workers...">
                            <div id="searchResults" style="position:absolute;width:100%;background:#fff;border:1px solid var(--outline-variant);box-shadow:var(--shadow-lg);margin-top:4px;border-radius:var(--radius);z-index:10;display:none;max-height:200px;overflow-y:auto"></div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vms mb-0" id="selectedWorkersTable">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Name</th>
                                        <th>CNIC</th>
                                        <th>Designation</th>
                                        <th>Vehicle No</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="workerListBody">
                                    <tr id="emptyRow" style="<?= !empty($requestData['workers']) ? 'display:none' : '' ?>">
                                        <td colspan="5" class="text-center py-4 text-muted-vms italic">No workers added yet. Search and add workers from above.</td>
                                    </tr>
                                    <?php if (!empty($requestData['workers'])): ?>
                                        <?php foreach ($requestData['workers'] as $w): ?>
                                            <tr id="worker-row-<?= $w['id'] ?>">
                                                <td class="ps-3">
                                                    <input type="hidden" name="worker_ids[]" value="<?= $w['id'] ?>">
                                                    <?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($w['cnic']) ?></td>
                                                <td><?= htmlspecialchars($w['designation']) ?></td>
                                                <td>
                                                    <input type="text" name="worker_vehicles[]" class="form-control form-control-sm form-control-vms" style="width:100px" placeholder="Veh #" value="<?= htmlspecialchars($w['worker_vehicle'] ?? '') ?>">
                                                </td>
                                                <td class="text-end pe-3">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeWorker(<?= $w['id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2" style="border-top:1px solid var(--outline-variant)">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($requestId ?? '') ?>">
                        <button type="button" class="btn btn-outline-cyan px-4 rounded-pill" onclick="submitForm(false)">Save Draft</button>
                        <button type="button" class="btn btn-cyan px-4 rounded-pill" onclick="submitForm(true)">Send for Permission</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const allWorkers = <?= json_encode($allWorkers) ?>;
const selectedWorkerIds = new Set(<?= json_encode(array_map('strval', array_column($requestData['workers'] ?? [], 'id'))) ?>);

const searchInput = document.getElementById('workerSearch');
const searchResults = document.getElementById('searchResults');
const workerListBody = document.getElementById('workerListBody');
const emptyRow = document.getElementById('emptyRow');

searchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    if (query.length < 1) {
        searchResults.style.display = 'none';
        return;
    }

    const filtered = allWorkers.filter(w => 
        !selectedWorkerIds.has(w.id.toString()) && 
        (w.first_name.toLowerCase().includes(query) || 
         w.cnic.toLowerCase().includes(query) || 
         w.designation.toLowerCase().includes(query))
    );

    if (filtered.length > 0) {
        searchResults.innerHTML = filtered.map(w => `
            <div class="p-2 border-bottom border-secondary search-item" onclick="addWorker(${w.id})" style="cursor: pointer;">
                <div class="fw-bold">${w.first_name} ${w.last_name}</div>
                <small class="text-muted-vms">${w.cnic} | ${w.designation}</small>
            </div>
        `).join('');
        searchResults.style.display = 'block';
    } else {
        searchResults.innerHTML = '<div class="p-2 text-muted-vms">No matching workers found</div>';
        searchResults.style.display = 'block';
    }
});

function addWorker(workerId) {
    const worker = allWorkers.find(w => w.id == workerId);
    if (!worker) return;

    selectedWorkerIds.add(workerId.toString());
    emptyRow.style.display = 'none';
    searchResults.style.display = 'none';
    searchInput.value = '';

    const row = document.createElement('tr');
    row.id = `worker-row-${workerId}`;
    row.innerHTML = `
        <td class="ps-3">
            <input type="hidden" name="worker_ids[]" value="${worker.id}">
            ${worker.first_name} ${worker.last_name}
        </td>
        <td>${worker.cnic}</td>
        <td>${worker.designation}</td>
        <td>
            <input type="text" name="worker_vehicles[]" class="form-control form-control-sm form-control-vms" style="width:100px" placeholder="Veh #">
        </td>
        <td class="text-end pe-3">
            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeWorker(${worker.id})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    workerListBody.appendChild(row);
}

function removeWorker(workerId) {
    document.getElementById(`worker-row-${workerId}`).remove();
    selectedWorkerIds.delete(workerId.toString());
    if (selectedWorkerIds.size === 0) {
        emptyRow.style.display = '';
    }
}

document.addEventListener('click', function(e) {
    if (!searchResults.contains(e.target) && e.target !== searchInput) {
        searchResults.style.display = 'none';
    }
});

function submitForm(sendForPermission) {
    const form = document.getElementById('entryForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.send_for_permission = sendForPermission;
    
    // Handle arrays for workers
    data.worker_ids = formData.getAll('worker_ids[]');
    data.worker_vehicles = formData.getAll('worker_vehicles[]');

    fetch('../../../api/vendor_entry_add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => {
                window.location.href = 'list.php';
            }, 1500);
        } else {
            showToast('Error: ' + result.message, 'danger');
        }
    })
    .catch(error => {
        showToast('An error occurred. Please try again.', 'danger');
        console.error('Error:', error);
    });
}
</script>

<style>
.search-item:hover {
    background: var(--surface-dim) !important;
}
.italic { font-style: italic; }
</style>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
