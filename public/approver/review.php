<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['approver','super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/AuditLog.php';
require_once __DIR__ . '/../../classes/Notification.php';

$vendorModel = new Vendor();
$id = (int)($_GET['id'] ?? 0);
$vendor = $vendorModel->getById($id);
if (!$vendor) { header('Location: pending.php'); exit; }

$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $comments = trim($_POST['comments'] ?? '');
    $audit = new AuditLog();
    $notif = new Notification();
    if ($action === 'approve') {
        if ($vendorModel->approve($id, $_SESSION['user_id'], $comments)) {
            $audit->log($_SESSION['user_id'], 'vendor_approved', 'vendor', $id);
            
            $title = "Vendor Account Approved";
            $msg = "Your vendor registration has been approved by the approver. You now have full dashboard access!";
            if (!empty($comments)) {
                $msg .= " Remarks: \"{$comments}\"";
            }
            $notif->create($vendor['user_id'], $title, $msg, '/VendorM/public/vendor/dashboard.php');

            $success = 'Vendor approved successfully.';
            $vendor = $vendorModel->getById($id);
        } else { $error = 'Failed to approve.'; }
    } elseif ($action === 'reject') {
        if (!$comments) { $error = 'Comments required for rejection.'; }
        else {
            if ($vendorModel->reject($id, $_SESSION['user_id'], $comments)) {
                $audit->log($_SESSION['user_id'], 'vendor_rejected', 'vendor', $id);
                
                $title = "Vendor Account Rejected";
                $msg = "Your vendor registration has been rejected by the approver. Remarks: \"{$comments}\"";
                $notif->create($vendor['user_id'], $title, $msg, null);

                $success = 'Vendor rejected.';
                $vendor = $vendorModel->getById($id);
            } else { $error = 'Failed to reject.'; }
        }
    }
}

$pageTitle = 'Review Vendor';
require_once __DIR__ . '/../../includes/sidebar_approver.php';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header">
        <h1><i class="bi bi-search"></i> Review: <?= htmlspecialchars($vendor['company_name']) ?></h1>
        <p>Status: <span class="badge badge-<?= $vendor['verification_status'] === 'verified' ? 'approved' : ($vendor['verification_status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= ucfirst($vendor['verification_status']) ?></span></p>
    </div>
    <?php if ($success): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast(<?= json_encode($success) ?>, 'success');
    });
    </script>
    <?php endif; ?>
    <?php if ($error): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast(<?= json_encode($error) ?>, 'danger');
    });
    </script>
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-building"></i> Company Information</div><div class="card-body">
                <div class="row g-2">
                    <div class="col-6"><label class="form-label">Company Name</label><p><?= htmlspecialchars($vendor['company_name']) ?></p></div>
                    <div class="col-6"><label class="form-label">Type</label><p><?= htmlspecialchars($vendor['type_name']) ?> <?= $vendor['subtype_name'] ? '/ ' . htmlspecialchars($vendor['subtype_name']) : '' ?></p></div>
                    <div class="col-6"><label class="form-label">Reg No</label><p><?= htmlspecialchars($vendor['company_registration_no'] ?? 'N/A') ?></p></div>
                    <div class="col-6"><label class="form-label">NTN</label><p><?= htmlspecialchars($vendor['ntn_number'] ?? 'N/A') ?></p></div>
                    <div class="col-6"><label class="form-label">Years in Business</label><p><?= $vendor['years_in_business'] ?? 'N/A' ?></p></div>
                    <div class="col-6"><label class="form-label">Employees</label><p><?= $vendor['number_of_employees'] ?? 'N/A' ?></p></div>
                </div>
            </div></div>
            <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-person"></i> Contact</div><div class="card-body">
                <div class="row g-2">
                    <div class="col-6"><label class="form-label">Primary Contact</label><p><?= htmlspecialchars($vendor['primary_contact_name']) ?></p></div>
                    <div class="col-6"><label class="form-label">Phone</label><p><?= htmlspecialchars($vendor['primary_contact_phone']) ?></p></div>
                    <div class="col-6"><label class="form-label">Email</label><p><?= htmlspecialchars($vendor['primary_contact_email']) ?></p></div>
                    <div class="col-6"><label class="form-label">City</label><p><?= htmlspecialchars($vendor['city']) ?></p></div>
                    <div class="col-12"><label class="form-label">Address</label><p><?= htmlspecialchars($vendor['address_line1']) ?></p></div>
                </div>
            </div></div>
            <div class="card card-vms mb-3"><div class="card-header"><i class="bi bi-bank"></i> Banking</div><div class="card-body">
                <div class="row g-2">
                    <div class="col-6"><label class="form-label">Bank</label><p><?= htmlspecialchars($vendor['bank_name'] ?? 'N/A') ?></p></div>
                    <div class="col-6"><label class="form-label">Account No</label><p><?= htmlspecialchars($vendor['bank_account_no'] ?? 'N/A') ?></p></div>
                    <div class="col-6"><label class="form-label">IBAN</label><p><?= htmlspecialchars($vendor['iban'] ?? 'N/A') ?></p></div>
                    <div class="col-6"><label class="form-label">Branch</label><p><?= htmlspecialchars($vendor['bank_branch'] ?? 'N/A') ?></p></div>
                </div>
            </div></div>
        </div>
        <div class="col-md-4">
            <?php if ($vendor['verification_status'] === 'pending' || $vendor['verification_status'] === 'under_review'): ?>
            <div class="card card-vms"><div class="card-header"><i class="bi bi-check2-square"></i> Decision</div><div class="card-body">
                <form method="POST" id="decisionForm">
                    <input type="hidden" name="action" id="decisionAction" value="">
                    <input type="hidden" name="comments" id="decisionComments" value="">
                    <button type="button" onclick="handleDecision('approve')" class="btn btn-cyan w-100 mb-2"><i class="bi bi-check-circle"></i> Approve Vendor</button>
                    <button type="button" onclick="handleDecision('reject')" class="btn btn-danger w-100"><i class="bi bi-x-circle"></i> Reject Vendor</button>
                </form>
            </div></div>
            <?php else: ?>
            <div class="card card-vms">
                <div class="card-header">Decision Made</div>
                <div class="card-body">
                    <div class="w-100 text-center py-2">
                        <?php if ($vendor['verification_status'] === 'verified'): ?>
                            <span class="badge bg-success rounded-pill px-3 py-2 w-100" style="font-size: 0.95rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Approved
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger rounded-pill px-3 py-2 w-100" style="font-size: 0.95rem;">
                                <i class="bi bi-x-circle-fill me-1"></i> Rejected
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($vendor['rejection_reason'])): ?>
                            <div class="mt-3 p-3 bg-light rounded text-start" style="font-size: 0.9rem; border-left: 4px solid var(--primary);">
                                <div class="text-muted small mb-1 fw-bold">REMARKS:</div>
                                <div style="color: var(--dark); font-style: italic;">"<?= htmlspecialchars($vendor['rejection_reason']) ?>"</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function handleDecision(action) {
    const title = action === 'approve' ? 'Approve Vendor' : 'Reject Vendor';
    const message = action === 'approve' ? 'Are you sure you want to approve this vendor? Please provide optional remarks.' : 'Are you sure you want to reject this vendor? Please provide mandatory remarks.';
    const btnText = action === 'approve' ? 'Approve' : 'Reject';
    const btnColor = action === 'approve' ? 'var(--success)' : 'var(--danger)';
    const requireRemarks = action === 'reject';

    showActionModal(title, message, btnText, btnColor, requireRemarks, function(remarks) {
        document.getElementById('decisionAction').value = action;
        document.getElementById('decisionComments').value = remarks;
        document.getElementById('decisionForm').submit();
    });
}
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
