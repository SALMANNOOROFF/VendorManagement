<?php
require_once __DIR__ . '/../../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../../classes/CompanyType.php';
$ct = new CompanyType();
$types = $ct->getAllWithSubtypes();
$pageTitle = 'Company Types';
require_once __DIR__ . '/../../../includes/sidebar_admin.php';
require_once __DIR__ . '/../../../includes/header.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-tags-fill"></i> Company Types</h1><p>Manage company categories</p></div>
    <?php foreach ($types as $type): ?>
    <div class="card card-vms mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-tag"></i> <?= htmlspecialchars($type['type_name']) ?></span>
            <span class="badge badge-active"><?= count($type['subtypes']) ?> subtypes</span>
        </div>
        <div class="card-body">
            <p class="text-muted-vms mb-2" style="font-size:0.85rem"><?= htmlspecialchars($type['description'] ?? '') ?></p>
            <?php if (!empty($type['subtypes'])): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($type['subtypes'] as $sub): ?>
                <span class="badge" style="background:var(--surface-dim);color:var(--on-surface-variant);padding:0.4rem 0.8rem"><?= htmlspecialchars($sub['subtype_name']) ?></span>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <span class="text-muted-vms">No subtypes defined</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
