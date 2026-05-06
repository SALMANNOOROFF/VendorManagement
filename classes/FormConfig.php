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
}
