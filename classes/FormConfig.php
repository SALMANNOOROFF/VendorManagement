<?php
require_once __DIR__ . '/../config/database.php';

class FormConfig {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getFields(string $formType): array {
        $stmt = $this->db->prepare("SELECT * FROM form_fields_config WHERE form_type = ? AND is_visible = 1 ORDER BY field_order ASC");
        $stmt->execute([$formType]);
        return $stmt->fetchAll();
    }

    public function getAllFields(string $formType): array {
        $stmt = $this->db->prepare("SELECT * FROM form_fields_config WHERE form_type = ? ORDER BY field_order ASC");
        $stmt->execute([$formType]);
        return $stmt->fetchAll();
    }

    public function toggleMandatory(string $formType, string $fieldName, bool $mandatory): bool {
        $stmt = $this->db->prepare("UPDATE form_fields_config SET is_mandatory = ?, updated_at = NOW() WHERE form_type = ? AND field_name = ?");
        return $stmt->execute([$mandatory, $formType, $fieldName]);
    }

    public function toggleVisible(string $formType, string $fieldName, bool $visible): bool {
        $stmt = $this->db->prepare("UPDATE form_fields_config SET is_visible = ?, updated_at = NOW() WHERE form_type = ? AND field_name = ?");
        return $stmt->execute([$visible, $formType, $fieldName]);
    }

    public function updateField(string $formType, string $fieldName, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $formType;
        $params[] = $fieldName;
        $sql = "UPDATE form_fields_config SET " . implode(", ", $fields) . ", updated_at = NOW() WHERE form_type = ? AND field_name = ?";
        return $this->db->prepare($sql)->execute($params);
    }

    public function addField(array $data): bool {
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $placeholders = implode(", ", array_fill(0, count($keys), "?"));
        $sql = "INSERT INTO form_fields_config ($fields) VALUES ($placeholders)";
        return $this->db->prepare($sql)->execute(array_values($data));
    }
}
