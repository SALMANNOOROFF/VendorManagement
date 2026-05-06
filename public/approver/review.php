<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['approver','super_admin']);
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/AuditLog.php';

$vendorModel = new Vendor();
$id = (int)($_GET['id'] ?? 0);
$vendor = $vendorModel->getById($id);
if (!$vendor) { header('Location: pending.php'); exit; }

$success = ''; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $comments = trim($_POST['comments'] ?? '');
    $audit = new AuditLog();
    if ($action === 'approve') {
        if ($vendorModel->approve($id, $_SESSION['user_id'], $comments)) {
            $audit->log($_SESSION['user_id'], 'vendor_approved', 'vendor', $id);
            $success = 'Vendor approved successfully.';
            $vendor = $vendorModel->getById($id);
        } else { $error = 'Failed to approve.'; }
    } elseif ($action === 'reject') {
        if (!$comments) { $error = 'Comments required for rejection.'; }
        else {
            if ($vendorModel->reject($id, $_SESSION['user_id'], $comments)) {
                $audit->log($_SESSION['user_id'], 'vendor_rejected', 'vendor', $id);
                $success = 'Vendor rejected.';
                $vendor = $vendorModel->getById($id);
            } else { $error = 'Failed to reject.'; }
        }
    }
}

$pageTitle = 'Review Vendor';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar_approver.php';
?>
<div class="main-content fade-in">
    <div class="page-header">
        <h1><i class="bi bi-search text-cyan"></i> Review: <?= htmlspecialchars($vendor['company_name']) ?></h1>
        <p>Status: <span class="badge badge-<?= $vendor['verification_status'] === 'verified' ? 'approved' : ($vendor['verification_status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= ucfirst($vendor['verification_status']) ?></span></p>
    </div>
    <?php if ($success): ?><div class="alert alert-vms alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-vms alert-danger"><?= $error ?></div><?php endif; ?>
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
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Comments</label><textarea name="comments" class="form-control form-control-vms" rows="4" placeholder="Required for rejection"></textarea></div>
                    <button type="submit" name="action" value="approve" class="btn btn-cyan w-100 mb-2"><i class="bi bi-check-circle"></i> Approve Vendor</button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger w-100"><i class="bi bi-x-circle"></i> Reject Vendor</button>
                </form>
            </div></div>
            <?php else: ?>
            <div class="card card-vms"><div class="card-header">Decision Made</div><div class="card-body">
                <p>This vendor has been <strong><?= $vendor['verification_status'] ?></strong>.</p>
                <?php if ($vendor['rejection_reason']): ?><p class="text-muted-vms">Reason: <?= htmlspecialchars($vendor['rejection_reason']) ?></p><?php endif; ?>
            </div></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
