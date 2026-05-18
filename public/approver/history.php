<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['approver','super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/EntryRequest.php';

$v = new Vendor();
$entryModel = new EntryRequest();

$approvedVendors = $v->getAll(['verification_status' => 'verified']);
$rejectedVendors = $v->getAll(['verification_status' => 'rejected']);

$approvedEntries = $entryModel->getHistory('approved');
$rejectedEntries = $entryModel->getHistory('rejected');

$pageTitle = 'Review History';
require_once __DIR__ . '/../../includes/sidebar_approver.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header mb-4">
        <h1><i class="bi bi-clock-history"></i> Review History</h1>
    </div>

    <!-- Main Navigation Pills for Vendor vs Entry Requests -->
    <ul class="nav nav-pills mb-4" id="mainHistoryTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="vendors-tab" data-bs-toggle="pill" data-bs-target="#vendors-pane" type="button" role="tab" style="font-weight: 500;">
                <i class="bi bi-briefcase me-2"></i>Vendor Registrations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="entries-tab" data-bs-toggle="pill" data-bs-target="#entries-pane" type="button" role="tab" style="font-weight: 500;">
                <i class="bi bi-door-open me-2"></i>Entry Requests
            </button>
        </li>
    </ul>

    <!-- Main Tab Content -->
    <div class="tab-content" id="mainHistoryTabContent">
        
        <!-- VENDORS PANE -->
        <div class="tab-pane fade show active" id="vendors-pane" role="tabpanel">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#approved-vendors" style="color:var(--success)">
                        Approved (<?= count($approvedVendors) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#rejected-vendors" style="color:var(--danger)">
                        Rejected (<?= count($rejectedVendors) ?>)
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Approved Vendors -->
                <div class="tab-pane fade show active" id="approved-vendors">
                    <div class="card card-vms"><div class="card-body p-0"><div class="table-responsive">
                        <table class="table table-vms mb-0">
                            <thead><tr><th>Company</th><th>Type</th><th>Contact</th><th>Date Approved</th><th class="text-center">Action</th></tr></thead>
                            <tbody>
                            <?php foreach ($approvedVendors as $a): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($a['company_name']) ?></strong></td>
                                <td><?= htmlspecialchars($a['type_name']) ?></td>
                                <td><?= htmlspecialchars($a['primary_contact_name']) ?></td>
                                <td class="text-muted-vms"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="review.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-cyan rounded-pill px-3">
                                        <i class="bi bi-eye-fill"></i> Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($approvedVendors)): ?><tr><td colspan="5" class="empty-state">No approved vendors</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div></div></div>
                </div>
                <!-- Rejected Vendors -->
                <div class="tab-pane fade" id="rejected-vendors">
                    <div class="card card-vms"><div class="card-body p-0"><div class="table-responsive">
                        <table class="table table-vms mb-0">
                            <thead><tr><th>Company</th><th>Reason</th><th>Date Rejected</th><th class="text-center">Action</th></tr></thead>
                            <tbody>
                            <?php foreach ($rejectedVendors as $rv): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($rv['company_name']) ?></strong></td>
                                <td class="text-muted-vms"><?= htmlspecialchars($rv['rejection_reason'] ?? 'N/A') ?></td>
                                <td class="text-muted-vms"><?= date('d M Y', strtotime($rv['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="review.php?id=<?= $rv['id'] ?>" class="btn btn-sm btn-outline-cyan rounded-pill px-3">
                                        <i class="bi bi-eye-fill"></i> Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($rejectedVendors)): ?><tr><td colspan="4" class="empty-state">No rejected vendors</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div></div></div>
                </div>
            </div>
        </div>

        <!-- ENTRY REQUESTS PANE -->
        <div class="tab-pane fade" id="entries-pane" role="tabpanel">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#approved-entries" style="color:var(--success)">
                        Approved (<?= count($approvedEntries) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#rejected-entries" style="color:var(--danger)">
                        Rejected (<?= count($rejectedEntries) ?>)
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Approved Entries -->
                <div class="tab-pane fade show active" id="approved-entries">
                    <div class="card card-vms"><div class="card-body p-0"><div class="table-responsive">
                        <table class="table table-vms mb-0">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Company</th>
                                    <th>Place of Work</th>
                                    <th>Type</th>
                                    <th>Remarks</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($approvedEntries as $ae): ?>
                            <tr>
                                <td>#REQ-<?= str_pad($ae['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><strong><?= htmlspecialchars($ae['company_name']) ?></strong></td>
                                <td><?= htmlspecialchars($ae['place_of_work']) ?></td>
                                <td><?= htmlspecialchars($ae['type_of_worker']) ?></td>
                                <td class="text-muted-vms" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($ae['remarks'] ?? 'N/A') ?></td>
                                <td class="text-muted-vms"><?= date('d M Y, h:i A', strtotime($ae['updated_at'])) ?></td>
                                <td class="text-center">
                                    <a href="entry_requests/review.php?id=<?= $ae['id'] ?>" class="btn btn-sm btn-outline-cyan rounded-pill px-3">
                                        <i class="bi bi-eye-fill"></i> Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($approvedEntries)): ?><tr><td colspan="7" class="empty-state">No approved entry requests</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div></div></div>
                </div>
                <!-- Rejected Entries -->
                <div class="tab-pane fade" id="rejected-entries">
                    <div class="card card-vms"><div class="card-body p-0"><div class="table-responsive">
                        <table class="table table-vms mb-0">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Company</th>
                                    <th>Place of Work</th>
                                    <th>Type</th>
                                    <th>Rejection Remarks</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($rejectedEntries as $re): ?>
                            <tr>
                                <td>#REQ-<?= str_pad($re['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><strong><?= htmlspecialchars($re['company_name']) ?></strong></td>
                                <td><?= htmlspecialchars($re['place_of_work']) ?></td>
                                <td><?= htmlspecialchars($re['type_of_worker']) ?></td>
                                <td class="text-muted-vms" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($re['remarks'] ?? 'N/A') ?></td>
                                <td class="text-muted-vms"><?= date('d M Y, h:i A', strtotime($re['updated_at'])) ?></td>
                                <td class="text-center">
                                    <a href="entry_requests/review.php?id=<?= $re['id'] ?>" class="btn btn-sm btn-outline-cyan rounded-pill px-3">
                                        <i class="bi bi-eye-fill"></i> Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($rejectedEntries)): ?><tr><td colspan="7" class="empty-state">No rejected entry requests</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div></div></div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
