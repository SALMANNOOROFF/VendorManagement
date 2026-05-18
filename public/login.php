<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $auth = new Auth();
        $result = $auth->login($email, $password);
        if ($result['success']) {
            $map = ['super_admin'=>'/admin/dashboard.php','approver'=>'/approver/dashboard.php','vendor'=>'/vendor/dashboard.php','worker'=>'/worker/dashboard.php'];
            header('Location: /VendorM/public' . ($map[$result['role']] ?? '/'));
            exit;
        }
        $error = $result['message'];
    } else {
        $error = 'Please enter email and password.';
    }
}
if (isset($_SESSION['user_id'])) {
    header('Location: /VendorM/public/');
    exit;
}
$hideNav = true;
$pageTitle = 'Sign In';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrapper">
    <div class="auth-card fade-in">
        <div class="logo">
            <i class="bi bi-shield-check" style="font-size:2.5rem;color:var(--primary)"></i>
            <h2>VMS Portal</h2>
            <p>Sign in to your account</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-vms alert-danger mb-3"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-vms alert-success mb-3"><i class="bi bi-check-circle"></i> Registration submitted! Await approval.</div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control form-control-vms" placeholder="admin@vms.local" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-vms" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-cyan w-100 mb-3" style="padding:0.7rem">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </button>
        </form>
        <div class="text-center">
            <span style="color:var(--gray-mid);font-size:0.85rem">New vendor? </span>
            <a href="vendor/register.php" style="font-size:0.85rem">Register here</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
