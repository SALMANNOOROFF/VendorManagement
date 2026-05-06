<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../classes/User.php';
require_once __DIR__ . '/../../../config/database.php';

$userModel = new User();
$id = (int)($_GET['id'] ?? 0);
$u = $userModel->getById($id);
if (!$u) { header('Location: list.php'); exit; }

$db = Database::getInstance();
$roles = $db->query("SELECT * FROM roles WHERE is_active = 1")->fetchAll();
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $newStatus = $_POST['status'] ?? $u['status'];
    $newRoleId = (int)($_POST['role_id'] ?? $u['role_id']);
    if ($newStatus !== $u['status']) $data['status'] = $newStatus;
    if ($newRoleId !== (int)$u['role_id']) $data['role_id'] = $newRoleId;
    if (!empty($_POST['password'])) $data['password_hash'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    if (!empty($data)) { $userModel->update($id, $data); $success = 'User updated.'; $u = $userModel->getById($id); }
}

$pageTitle = 'Edit User';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-pencil-fill text-cyan"></i> Edit User</h1><p>Editing: <?= htmlspecialchars($u['username']) ?></p></div>
    <?php if ($error): ?><div class="alert alert-vms alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-vms alert-success"><?= $success ?></div><?php endif; ?>
    <div class="card card-vms" style="max-width:600px">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3"><label class="form-label">Username</label><input type="text" class="form-control form-control-vms" value="<?= htmlspecialchars($u['username']) ?>" disabled></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="text" class="form-control form-control-vms" value="<?= htmlspecialchars($u['email']) ?>" disabled></div>
                <div class="mb-3"><label class="form-label">New Password (leave blank to keep)</label><input type="password" name="password" class="form-control form-control-vms" minlength="6"></div>
                <div class="mb-3"><label class="form-label">Role</label>
                    <select name="role_id" class="form-select form-control-vms">
                        <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['role_display']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Status</label>
                    <select name="status" class="form-select form-control-vms">
                        <?php foreach (['pending','active','suspended','rejected'] as $s): ?><option value="<?= $s ?>" <?= $u['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-cyan"><i class="bi bi-check-lg"></i> Save Changes</button>
                <a href="list.php" class="btn btn-outline-cyan ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
