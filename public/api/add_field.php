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
$type      = $_POST['field_type'] ?? 'text';
$group     = $_POST['field_group'] ?? 'Account';

if (!$formType || !$fieldName || !$label) {
    echo json_encode(['success' => false, 'message' => 'Field name and label are required']); exit;
}

$fc = new FormConfig();
$result = $fc->addField([
    'form_type' => $formType,
    'field_name' => $fieldName,
    'field_label' => $label,
    'field_type' => $type,
    'field_group' => $group,
    'is_visible' => 1,
    'is_mandatory' => 0,
    'field_order' => 99
]);

echo json_encode(['success' => $result]);
