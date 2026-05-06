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
$label     = $_POST['field_label'] ?? '';

if (!$formType || !$fieldName || !$label) {
    echo json_encode(['success' => false, 'message' => 'Invalid params']); exit;
}

$fc = new FormConfig();
$result = $fc->updateField($formType, $fieldName, ['field_label' => $label]);

echo json_encode(['success' => $result]);
