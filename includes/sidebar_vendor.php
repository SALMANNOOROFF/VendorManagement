<?php $appUrl = '/VendorM/public'; $current = basename($_SERVER['PHP_SELF']); ?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="sidebar" id="sidebarNav">
    <div class="sidebar-mobile-header d-md-none">
        <div class="d-flex align-items-center gap-2 px-3 py-2" style="border-bottom:1px solid var(--navy-mid)">
            <i class="bi bi-person-circle" style="color:var(--cyan);font-size:1.3rem"></i>
            <div>
                <div style="color:var(--white);font-size:0.85rem;font-weight:600"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></div>
                <div style="color:var(--gray-mid);font-size:0.7rem"><?= ucfirst(str_replace('_', ' ', $_SESSION['role_name'] ?? '')) ?></div>
            </div>
        </div>
    </div>
    <div class="nav-section">Main</div>
    <a class="nav-link <?= $current === 'dashboard.php' ? 'active' : '' ?>" href="<?= $appUrl ?>/vendor/dashboard.php">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>
    <div class="nav-section">Company</div>
    <a class="nav-link <?= $current === 'profile.php' ? 'active' : '' ?>" href="<?= $appUrl ?>/vendor/profile.php">
        <i class="bi bi-building"></i> Company Profile
    </a>
    <a class="nav-link <?= $current === 'documents.php' ? 'active' : '' ?>" href="<?= $appUrl ?>/vendor/documents.php">
        <i class="bi bi-folder2-open"></i> Documents
    </a>
    <div class="nav-section">Workforce</div>
    <a class="nav-link <?= $current === 'list.php' ? 'active' : '' ?>" href="<?= $appUrl ?>/vendor/workers/list.php">
        <i class="bi bi-people-fill"></i> Workers
    </a>
    <a class="nav-link <?= $current === 'add.php' ? 'active' : '' ?>" href="<?= $appUrl ?>/vendor/workers/add.php">
        <i class="bi bi-person-plus-fill"></i> Add Worker
    </a>
    <div class="d-md-none" style="border-top:1px solid var(--navy-mid);margin-top:1rem;padding-top:0.5rem">
        <a class="nav-link" href="<?= $appUrl ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>
