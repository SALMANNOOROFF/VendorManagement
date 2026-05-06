<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../config/database.php';
$db = Database::getInstance();
$roles = $db->query("SELECT * FROM roles ORDER BY id")->fetchAll();
$pageTitle = 'Roles';
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-shield-lock-fill text-cyan"></i> Roles</h1><p>System role definitions</p></div>
    <div class="row g-3">
        <?php foreach ($roles as $r): ?>
        <div class="col-md-6">
            <div class="card card-vms">
                <div class="card-header"><i class="bi bi-shield-check"></i> <?= htmlspecialchars($r['role_display']) ?></div>
                <div class="card-body">
                    <p class="text-muted-vms mb-2"><?= htmlspecialchars($r['description']) ?></p>
                    <div><code style="color:var(--cyan);font-size:0.8rem"><?= $r['role_name'] ?></code></div>
                    <div class="mt-2"><span class="badge <?= $r['is_active'] ? 'badge-approved' : 'badge-rejected' ?>"><?= $r['is_active'] ? 'Active' : 'Inactive' ?></span></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
