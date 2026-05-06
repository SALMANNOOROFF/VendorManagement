<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../classes/User.php';
require_once __DIR__ . '/../../../config/database.php';

$error = ''; $success = '';
$db = Database::getInstance();
$roles = $db->query("SELECT * FROM roles WHERE is_active = 1 ORDER BY id")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();
    $data = [
        'username'   => trim($_POST['username'] ?? ''),
        'email'      => trim($_POST['email'] ?? ''),
        'password'   => $_POST['password'] ?? '',
        'role_id'    => (int)($_POST['role_id'] ?? 0),
        'status'     => 'active',
        'created_by' => $_SESSION['user_id']
    ];
    if (!$data['username'] || !$data['email'] || !$data['password'] || !$data['role_id']) {
        $error = 'All fields are required.';
    } elseif ($userModel->emailExists($data['email'])) {
        $error = 'Email already exists.';
    } elseif ($userModel->usernameExists($data['username'])) {
        $error = 'Username already exists.';
    } else {
        $userModel->create($data);
        $success = 'User created successfully.';
    }
}

$pageTitle = 'Create User';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-person-plus-fill text-cyan"></i> Create User</h1><p>Add a new system user</p></div>
    <?php if ($error): ?><div class="alert alert-vms alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-vms alert-success"><?= $success ?></div><?php endif; ?>
    <div class="card card-vms" style="max-width:600px">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3"><label class="form-label">Username *</label><input type="text" name="username" class="form-control form-control-vms" required></div>
                <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control form-control-vms" required></div>
                <div class="mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control form-control-vms" required minlength="6"></div>
                <div class="mb-3"><label class="form-label">Role *</label>
                    <select name="role_id" class="form-select form-control-vms" required>
                        <option value="">-- Select Role --</option>
                        <?php foreach ($roles as $r): ?><option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['role_display']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-cyan"><i class="bi bi-check-lg"></i> Create User</button>
                <a href="list.php" class="btn btn-outline-cyan ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
