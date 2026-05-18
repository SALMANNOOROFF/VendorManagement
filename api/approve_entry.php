<?php
require_once __DIR__ . '/../middleware/role_check.php';
checkRole(['super_admin', 'approver']);
require_once __DIR__ . '/../classes/EntryRequest.php';

require_once __DIR__ . '/../classes/Notification.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
$id = $data['id'] ?? null;
$action = $data['action'] ?? null; // 'approve' or 'reject'

if (!$id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$remarks = $data['remarks'] ?? null;

$entryModel = new EntryRequest();
$request = $entryModel->getById($id);

if ($action === 'approve') {
    $result = $entryModel->approve($id, $remarks);
} else {
    $result = $entryModel->reject($id, $remarks);
}

if ($result) {
    if ($request) {
        $notif = new Notification();
        $title = "Entry Request " . ($action === 'approve' ? 'Approved' : 'Rejected');
        $msg = "Your entry permission request #REQ-" . str_pad($id, 5, '0', STR_PAD_LEFT) . " has been " . ($action === 'approve' ? 'approved' : 'rejected') . " by the approver.";
        if (!empty($remarks)) {
            $msg .= " Remarks: \"{$remarks}\"";
        }
        $link = "/VendorM/public/vendor/entry/list.php";
        $notif->create($request['vendor_user_id'], $title, $msg, $link);
    }
    echo json_encode(['success' => true, 'message' => "Request has been {$action}d successfully."]);
} else {
    echo json_encode(['success' => false, 'message' => "Failed to process request."]);
}
