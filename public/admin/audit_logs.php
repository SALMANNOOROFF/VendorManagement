<?php
require_once __DIR__ . '/../../middleware/role_check.php';
checkRole(['super_admin']);
require_once __DIR__ . '/../../classes/AuditLog.php';
$al = new AuditLog();
$logs = $al->getAll(100);
$pageTitle = 'Audit Logs';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar_admin.php';
?>
<div class="main-content fade-in">
    <div class="page-header"><h1><i class="bi bi-clock-history text-cyan"></i> Audit Logs</h1><p>System activity trail</p></div>
    <div class="card card-vms">
        <div class="card-body p-0">
            <table class="table table-vms mb-0">
                <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Entity</th><th>IP</th></tr></thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td class="text-muted-vms"><?= date('d M Y H:i', strtotime($l['created_at'])) ?></td>
                    <td><?= htmlspecialchars($l['username'] ?? 'System') ?></td>
                    <td><span class="badge badge-active"><?= htmlspecialchars($l['action']) ?></span></td>
                    <td><?= htmlspecialchars(($l['entity_type'] ?? '') . ($l['entity_id'] ? ' #' . $l['entity_id'] : '')) ?></td>
                    <td class="text-muted-vms"><?= htmlspecialchars($l['ip_address'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?><tr><td colspan="5" class="empty-state"><i class="bi bi-inbox"></i>No logs yet</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
