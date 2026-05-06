<?php
require_once __DIR__ . '/../config/database.php';

class CompanyType {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->query("
            SELECT * FROM company_types WHERE is_active = 1 ORDER BY sort_order
        ")->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM company_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getSubtypes(int $typeId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM company_subtypes 
            WHERE company_type_id = ? AND is_active = 1 
            ORDER BY sort_order
        ");
        $stmt->execute([$typeId]);
        return $stmt->fetchAll();
    }

    public function create(string $name, ?string $description = null, int $sortOrder = 0): int {
        $stmt = $this->db->prepare("INSERT INTO company_types (type_name, description, sort_order) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $sortOrder]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        return $this->db->prepare("UPDATE company_types SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    }

    public function createSubtype(int $typeId, string $name, ?string $description = null, int $sortOrder = 0): int {
        $stmt = $this->db->prepare("INSERT INTO company_subtypes (company_type_id, subtype_name, description, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$typeId, $name, $description, $sortOrder]);
        return (int)$this->db->lastInsertId();
    }

    public function getAllWithSubtypes(): array {
        $types = $this->getAll();
        foreach ($types as &$type) {
            $type['subtypes'] = $this->getSubtypes($type['id']);
        }
        return $types;
    }
}
