<?php
require_once __DIR__ . '/../config/database.php';

class AuditLog {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function log(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, $oldValues = null, $newValues = null): void {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId, $action, $entityType, $entityId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    public function getAll(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT al.*, u.username FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
    }
}
