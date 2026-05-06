<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login(string $email, string $password): array {
        // Check brute force
        $stmt = $this->db->prepare("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        // Check if locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['success' => false, 'message' => 'Account locked. Try again later.'];
        }

        // Check status
        if ($user['status'] !== 'active') {
            $statusMessages = [
                'pending'   => 'Your account is pending approval.',
                'suspended' => 'Your account has been suspended.',
                'rejected'  => 'Your registration was rejected.',
            ];
            return ['success' => false, 'message' => $statusMessages[$user['status']] ?? 'Account inactive.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            // Increment login attempts
            $attempts = $user['login_attempts'] + 1;
            $lockedUntil = null;
            if ($attempts >= 5) {
                $lockedUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            }
            $stmt = $this->db->prepare("UPDATE users SET login_attempts = ?, locked_until = ? WHERE id = ?");
            $stmt->execute([$attempts, $lockedUntil, $user['id']]);
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        // Reset login attempts
        $this->db->prepare("UPDATE users SET login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?")
                 ->execute([$user['id']]);

        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['email']     = $user['email'];

        return ['success' => true, 'role' => $user['role_name']];
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
        }
        session_destroy();
    }

    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function userId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): ?string {
        return $_SESSION['role_name'] ?? null;
    }

    public static function username(): ?string {
        return $_SESSION['username'] ?? null;
    }
}
