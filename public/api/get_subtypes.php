<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/CompanyType.php';

$typeId = (int)($_GET['type_id'] ?? 0);
if (!$typeId) { echo json_encode(['subtypes' => []]); exit; }

$ct = new CompanyType();
echo json_encode(['subtypes' => $ct->getSubtypes($typeId)]);
