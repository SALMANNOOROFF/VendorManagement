<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/FormConfig.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

$formType  = $_POST['form_type'] ?? '';
$fieldName = $_POST['field_name'] ?? '';

if (!$formType || !$fieldName) {
    echo json_encode(['success' => false, 'message' => 'Invalid params']); exit;
}

$db = Database::getInstance();
$stmt = $db->prepare("DELETE FROM form_fields_config WHERE form_type = ? AND field_name = ?");
$result = $stmt->execute([$formType, $fieldName]);

echo json_encode(['success' => $result]);
