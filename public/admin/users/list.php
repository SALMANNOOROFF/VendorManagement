<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../classes/User.php';
$userModel = new User();
$users = $userModel->getAll();
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
?>
<div class="main-content fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div><h1><i class="bi bi-people-fill text-cyan"></i> Users</h1><p>Manage all system users</p></div>
        <a href="create.php" class="btn btn-cyan"><i class="bi bi-person-plus"></i> Create User</a>
    </div>
    <div class="card card-vms">
        <div class="card-body p-0">
            <table class="table table-vms mb-0">
                <thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge badge-active"><?= htmlspecialchars($u['role_display']) ?></span></td>
                    <td><span class="badge badge-<?= $u['status'] === 'active' ? 'approved' : ($u['status'] === 'rejected' ? 'rejected' : 'pending') ?>"><?= ucfirst($u['status']) ?></span></td>
                    <td class="text-muted-vms"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td><a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-outline-cyan btn-sm"><i class="bi bi-pencil"></i></a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
