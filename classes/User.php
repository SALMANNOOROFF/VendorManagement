<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, role_id, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role_id'],
            $data['status'] ?? 'pending',
            $data['created_by'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.role_name, r.role_display 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT u.*, r.role_name, r.role_display FROM users u JOIN roles r ON u.role_id = r.id WHERE 1=1";
        $params = [];

        if (!empty($filters['role_name'])) {
            $sql .= " AND r.role_name = ?";
            $params[] = $filters['role_name'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND u.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY u.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status, ?int $approvedBy = null): bool {
        $sql = "UPDATE users SET status = ?, updated_at = NOW()";
        $params = [$status];
        if ($approvedBy) {
            $sql .= ", approved_by = ?, approved_at = NOW()";
            $params[] = $approvedBy;
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;
        return $this->db->prepare($sql)->execute($params);
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        return $this->db->prepare($sql)->execute($params);
    }

    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $params = [$username];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function count(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
