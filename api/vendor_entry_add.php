<?php
require_once __DIR__ . '/../middleware/role_check.php';
checkRole(['vendor']);
require_once __DIR__ . '/../classes/Vendor.php';
require_once __DIR__ . '/../classes/EntryRequest.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
$vendorModel = new Vendor();
$vendor = $vendorModel->getByUserId($_SESSION['user_id']);

if (!$vendor) {
    echo json_encode(['success' => false, 'message' => 'Vendor not found']);
    exit;
}

$entryModel = new EntryRequest();
$requestId = $data['id'] ?? null;
$status = isset($data['send_for_permission']) && $data['send_for_permission'] ? 'pending' : 'draft';

$requestData = [
    'vendor_id' => $vendor['id'],
    'type_of_worker' => $data['type_of_worker'] ?? '',
    'place_of_work' => $data['place_of_work'] ?? '',
    'vehicle_no' => $data['main_vehicle_no'] ?? '',
    'status' => $status,
    'workers' => []
];

if (!empty($data['worker_ids']) && is_array($data['worker_ids'])) {
    foreach ($data['worker_ids'] as $index => $workerId) {
        $requestData['workers'][] = [
            'id' => $workerId,
            'vehicle_no' => $data['worker_vehicles'][$index] ?? null
        ];
    }
}

if ($requestId) {
    $existing = $entryModel->getById($requestId);
    if (!$existing || $existing['vendor_id'] != $vendor['id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    $result = $entryModel->update($requestId, $requestData);
} else {
    $requestId = $entryModel->create($requestData);
    $result = (bool)$requestId;
}

if ($result) {
    echo json_encode(['success' => true, 'message' => "Entry request saved successfully."]);
} else {
    echo json_encode(['success' => false, 'message' => "Failed to save entry request."]);
}
