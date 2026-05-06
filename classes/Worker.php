<?php
require_once __DIR__ . '/../config/database.php';

class Worker {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function add(int $userId, int $vendorId, array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO workers (
                user_id, vendor_id, first_name, last_name, cnic, date_of_birth, gender,
                nationality, phone, email, address, emergency_contact_name,
                emergency_contact_phone, emergency_relation, designation, department,
                employee_code, join_date, employment_type, monthly_salary, education_level,
                experience_years, cnic_front, cnic_back, profile_photo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $vendorId,
            $data['first_name'],
            $data['last_name'],
            $data['cnic'],
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null,
            $data['nationality'] ?? 'Pakistani',
            $data['phone'],
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['emergency_contact_name'] ?? null,
            $data['emergency_contact_phone'] ?? null,
            $data['emergency_relation'] ?? null,
            $data['designation'],
            $data['department'] ?? null,
            $data['employee_code'] ?? null,
            $data['join_date'],
            $data['employment_type'] ?? null,
            $data['monthly_salary'] ?? null,
            $data['education_level'] ?? null,
            $data['experience_years'] ?? 0,
            $data['cnic_front'] ?? null,
            $data['cnic_back'] ?? null,
            $data['profile_photo'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT w.*, v.company_name, u.email as user_email, u.username
            FROM workers w
            JOIN vendors v ON w.vendor_id = v.id
            JOIN users u ON w.user_id = u.id
            WHERE w.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT w.*, v.company_name, u.email as user_email, u.username
            FROM workers w
            JOIN vendors v ON w.vendor_id = v.id
            JOIN users u ON w.user_id = u.id
            WHERE w.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function getByVendor(int $vendorId): array {
        $stmt = $this->db->prepare("
            SELECT w.*, u.email as user_email, u.status as account_status
            FROM workers w
            JOIN users u ON w.user_id = u.id
            WHERE w.vendor_id = ?
            ORDER BY w.first_name ASC
        ");
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE workers SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        return $this->db->prepare($sql)->execute($params);
    }

    public function deactivate(int $id, string $reason): bool {
        $stmt = $this->db->prepare("
            UPDATE workers SET is_active = FALSE, deactivation_reason = ?, deactivated_at = NOW(), updated_at = NOW() WHERE id = ?
        ");
        return $stmt->execute([$reason, $id]);
    }

    public function activate(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE workers SET is_active = TRUE, deactivation_reason = NULL, deactivated_at = NULL, updated_at = NOW() WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function countByVendor(int $vendorId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM workers WHERE vendor_id = ?");
        $stmt->execute([$vendorId]);
        return (int)$stmt->fetchColumn();
    }

    public function countAll(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM workers WHERE 1=1";
        $params = [];
        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = ?";
            $params[] = $filters['is_active'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function cnicExists(string $cnic, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM workers WHERE cnic = ?";
        $params = [$cnic];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
