<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['vendor', 'super_admin', 'approver']);
require_once __DIR__ . '/../../../classes/EntryRequest.php';

$entryModel = new EntryRequest();
$id = $_GET['id'] ?? null;

if (!$id) { header('Location: list.php'); exit; }

$request = $entryModel->getById($id);
if (!$request) { header('Location: list.php'); exit; }

$pageTitle = 'Request Details';
require_once __DIR__ . '/../../../includes/sidebar_vendor.php';
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="bi bi-shield-check"></i> Request Details</h1>
            <p class="text-muted-vms mb-0">#REQ-<?= str_pad($request['id'], 5, '0', STR_PAD_LEFT) ?></p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($request['status'] === 'draft' || $request['status'] === 'pending'): ?>
                <a href="add.php?id=<?= $request['id'] ?>" class="btn btn-outline-warning rounded-pill px-4">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            <?php endif; ?>
            <a href="document.php?id=<?= $request['id'] ?>" target="_blank" class="btn btn-cyan rounded-pill px-4">
                <i class="bi bi-file-earmark-pdf"></i> Document View
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Status Card -->
        <div class="col-md-4">
            <div class="card card-vms" style="height:100%">
                <div class="card-header"><i class="bi bi-info-circle"></i> Status & Info</div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="text-muted-vms d-block small mb-1">CURRENT STATUS</label>
                        <span class="badge badge-<?= $request['status'] === 'approved' ? 'approved' : ($request['status'] === 'rejected' ? 'rejected' : ($request['status'] === 'pending' ? 'pending' : 'secondary')) ?> fs-6 px-3 py-2">
                            <?= ucfirst($request['status']) ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted-vms d-block small">SUBMITTED ON</label>
                        <div><?= date('d M Y, h:i A', strtotime($request['created_at'])) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted-vms d-block small">TYPE OF WORKER</label>
                        <div><?= htmlspecialchars($request['type_of_worker']) ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted-vms d-block small">PLACE OF WORK</label>
                        <div><?= htmlspecialchars($request['place_of_work']) ?></div>
                    </div>
                    <?php if ($request['vehicle_no']): ?>
                    <div class="mb-3">
                        <label class="text-muted-vms d-block small">MAIN VEHICLE</label>
                        <div><?= htmlspecialchars($request['vehicle_no']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Workers List Card -->
        <div class="col-md-8">
            <div class="card card-vms">
                <div class="card-header">
                    <i class="bi bi-people"></i> Authorized Personnel
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vms mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Name</th>
                                    <th>CNIC</th>
                                    <th>Designation</th>
                                    <th>Mobile</th>
                                    <th class="pe-3">Vehicle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($request['workers'] as $w): ?>
                                <tr>
                                    <td class="ps-3">
                                        <strong><?= htmlspecialchars($w['first_name'] . ' ' . $w['last_name']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($w['cnic']) ?></td>
                                    <td><?= htmlspecialchars($w['designation']) ?></td>
                                    <td><?= htmlspecialchars($w['phone'] ?: 'N/A') ?></td>
                                    <td class="pe-3"><?= htmlspecialchars($w['worker_vehicle'] ?: ($request['vehicle_no'] ?: '-')) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
