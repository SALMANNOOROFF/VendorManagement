<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role_name'];
    $map = ['super_admin'=>'/admin/dashboard.php','approver'=>'/approver/dashboard.php','vendor'=>'/vendor/dashboard.php','worker'=>'/worker/dashboard.php'];
    header('Location: /VendorM/public' . ($map[$role] ?? '/login.php'));
    exit;
}
$hideNav = true;
$pageTitle = 'Welcome';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="hero-section">
    <div style="position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;pointer-events:none">
        <div style="position:absolute;width:600px;height:600px;background:radial-gradient(circle,rgba(0,188,212,0.06),transparent 70%);top:-200px;right:-200px;border-radius:50%"></div>
        <div style="position:absolute;width:400px;height:400px;background:radial-gradient(circle,rgba(0,188,212,0.04),transparent 70%);bottom:-100px;left:-100px;border-radius:50%"></div>
    </div>
    <div class="hero-content" style="position:relative;z-index:1">
        <div style="margin-bottom:2rem">
            <i class="bi bi-shield-check" style="font-size:4rem;color:var(--cyan);filter:drop-shadow(0 0 20px rgba(0,188,212,0.3))"></i>
        </div>
        <h1>Vendor Management<br><span>System</span></h1>
        <p>Enterprise-grade vendor registration, approval workflow, and workforce management platform. Streamline your procurement supply chain with precision.</p>
        <div class="hero-actions">
            <a href="login.php" class="btn btn-cyan btn-lg"><i class="bi bi-box-arrow-in-right"></i> Sign In</a>
            <a href="vendor/register.php" class="btn btn-outline-cyan btn-lg"><i class="bi bi-building"></i> Vendor Registration</a>
        </div>
        <div style="margin-top:3rem;display:flex;gap:3rem;justify-content:center;flex-wrap:wrap">
            <div style="text-align:center"><div style="font-size:1.8rem;font-weight:700;color:var(--cyan)">4</div><div style="font-size:0.75rem;color:var(--gray-mid);text-transform:uppercase;letter-spacing:0.05em">Role-Based Access</div></div>
            <div style="text-align:center"><div style="font-size:1.8rem;font-weight:700;color:var(--cyan)">100%</div><div style="font-size:0.75rem;color:var(--gray-mid);text-transform:uppercase;letter-spacing:0.05em">Audit Trail</div></div>
            <div style="text-align:center"><div style="font-size:1.8rem;font-weight:700;color:var(--cyan)">24/7</div><div style="font-size:0.75rem;color:var(--gray-mid);text-transform:uppercase;letter-spacing:0.05em">Self-Service</div></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
