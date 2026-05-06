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
$key       = $_POST['key'] ?? '';
$value     = (int)($_POST['value'] ?? 0);

if (!$formType || !$fieldName || !in_array($key, ['mandatory','visible'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid params']); exit;
}

$fc = new FormConfig();
$result = $key === 'mandatory'
    ? $fc->toggleMandatory($formType, $fieldName, (bool)$value)
    : $fc->toggleVisible($formType, $fieldName, (bool)$value);

echo json_encode(['success' => $result]);
