<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin', 'approver']);
require_once __DIR__ . '/../../../classes/EntryRequest.php';

$entryModel = new EntryRequest();
$pendingRequests = $entryModel->getAllPending();

$pageTitle = 'Pending Entry Requests';
require_once __DIR__ . '/../../../includes/sidebar_approver.php';
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="main-content fade-in">
    <div class="page-header">
        <h1><i class="bi bi-door-open"></i> Pending Entry Requests</h1>
        <p><?= count($pendingRequests) ?> request(s) awaiting review</p>
    </div>

    <div class="card card-vms">
        <div class="card-body p-0"><div class="table-responsive">
            <table class="table table-vms mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vendor Name</th>
                        <th>Type of Worker</th>
                        <th>Place of Work</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php if (empty($pendingRequests)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted-vms">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                No pending entry requests.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($pendingRequests as $req): ?>
                            <tr>
                                <td><strong>#<?= str_pad($req['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                <td><strong><?= htmlspecialchars($req['company_name']) ?></strong></td>
                                <td><?= htmlspecialchars($req['type_of_worker']) ?></td>
                                <td><?= htmlspecialchars($req['place_of_work']) ?></td>
                                <td class="text-muted-vms"><?= date('d M Y, h:i A', strtotime($req['created_at'])) ?></td>
                                <td>
                                    <a href="review.php?id=<?= $req['id'] ?>" class="btn btn-cyan btn-sm"><i class="bi bi-eye"></i> Review</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
