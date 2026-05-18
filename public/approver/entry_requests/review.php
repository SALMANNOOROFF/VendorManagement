<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin', 'approver']);
require_once __DIR__ . '/../../../classes/EntryRequest.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: list.php'); exit; }

$entryModel = new EntryRequest();
$request = $entryModel->getById($id);

if (!$request) {
    header('Location: list.php');
    exit;
}

$pageTitle = 'Review Entry Request';
require_once __DIR__ . '/../../../includes/sidebar_approver.php';
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 style="color:var(--primary)"><i class="bi bi-door-open"></i> Review Request #<?= str_pad($request['id'], 5, '0', STR_PAD_LEFT) ?></h1>
            <p style="font-size: 1.1rem; margin:0;"><?= htmlspecialchars($request['company_name']) ?></p>
        </div>
        <a href="list.php" class="btn btn-outline-secondary rounded-pill px-4">Back to List</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-vms" style="height:100%">
                <div class="card-header">Request Details</div>
                <div class="card-body">
                    <p><strong>Type of Worker:</strong><br><?= htmlspecialchars($request['type_of_worker']) ?></p>
                    <p><strong>Place of Work:</strong><br><?= htmlspecialchars($request['place_of_work']) ?></p>
                    <p><strong>Vehicle No:</strong><br><?= htmlspecialchars($request['vehicle_no'] ?: 'N/A') ?></p>
                    <p><strong>Phone:</strong><br><?= htmlspecialchars($request['primary_contact_phone']) ?></p>
                    <p><strong>Date Submitted:</strong><br><?= date('d M Y, h:i A', strtotime($request['created_at'])) ?></p>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <?php if ($request['status'] === 'pending'): ?>
                        <button class="btn btn-outline-danger px-4 rounded-pill w-100 me-2" onclick="processRequest(<?= $request['id'] ?>, 'reject')" id="btnReject">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                        <button class="btn px-4 rounded-pill w-100 ms-2 text-white" onclick="processRequest(<?= $request['id'] ?>, 'approve')" id="btnApprove" style="background-color: var(--success); border-color: var(--success);">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    <?php else: ?>
                        <div class="w-100 text-center py-2">
                            <?php if ($request['status'] === 'approved'): ?>
                                <span class="badge bg-success rounded-pill px-3 py-2 w-100" style="font-size: 0.95rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i> Approved
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill px-3 py-2 w-100" style="font-size: 0.95rem;">
                                    <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($request['remarks'])): ?>
                                <div class="mt-3 p-3 bg-light rounded text-start" style="font-size: 0.9rem; border-left: 4px solid var(--primary);">
                                    <div class="text-muted small mb-1 fw-bold">REMARKS:</div>
                                    <div style="color: var(--dark); font-style: italic;">"<?= htmlspecialchars($request['remarks']) ?>"</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-vms">
                <div class="card-header"><i class="bi bi-people"></i> Workers Included (<?= count($request['workers'] ?? []) ?>)</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vms mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>CNIC</th>
                                    <th>Designation</th>
                                    <th>Vehicle No</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($request['workers'])): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted-vms">No workers listed.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($request['workers'] as $w): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></strong></td>
                                        <td><?= htmlspecialchars($w['cnic']) ?></td>
                                        <td><?= htmlspecialchars($w['designation']) ?></td>
                                        <td><?= htmlspecialchars($w['worker_vehicle'] ?: '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function processRequest(id, action) {
    const title = action === 'approve' ? 'Approve Entry Request' : 'Reject Entry Request';
    const message = action === 'approve' ? 'Are you sure you want to approve this request? Please provide optional remarks.' : 'Are you sure you want to reject this request? Please provide mandatory remarks.';
    const btnText = action === 'approve' ? 'Approve' : 'Reject';
    const btnColor = action === 'approve' ? 'var(--success)' : 'var(--danger)';
    const requireRemarks = action === 'reject';

    showActionModal(title, message, btnText, btnColor, requireRemarks, function(remarks) {
        const btnReject = document.getElementById('btnReject');
        const btnApprove = document.getElementById('btnApprove');
        
        btnReject.disabled = true;
        btnApprove.disabled = true;

        fetch('../../../api/approve_entry.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, action: action, remarks: remarks })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.href = 'list.php';
                }, 1500);
            } else {
                showToast('Error: ' + data.message, 'danger');
                btnReject.disabled = false;
                btnApprove.disabled = false;
            }
        })
        .catch(err => {
            showToast('An error occurred. Please try again.', 'danger');
            btnReject.disabled = false;
            btnApprove.disabled = false;
        });
    });
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
