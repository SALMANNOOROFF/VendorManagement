<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Worker.php';
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/FileUpload.php';
require_once __DIR__ . '/../../classes/AuditLog.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']); exit;
}
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'vendor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

$required = ['first_name','last_name','cnic','phone','designation','join_date','vendor_id'];
foreach ($required as $f) {
    if (empty(trim($_POST[$f] ?? ''))) {
        echo json_encode(['success' => false, 'message' => "'{$f}' is required."]); exit;
    }
}

$workerModel = new Worker();
if ($workerModel->cnicExists($_POST['cnic'])) {
    echo json_encode(['success' => false, 'message' => 'CNIC already registered.']); exit;
}

$userModel = new User();
$upload = new FileUpload();

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    // Create user for worker
    $workerUsername = 'w_' . preg_replace('/[^0-9]/', '', $_POST['cnic']);
    $workerEmail = $workerUsername . '@worker.vms.local';
    $userId = $userModel->create([
        'username' => $workerUsername,
        'email' => $workerEmail,
        'password' => 'Worker@' . substr(preg_replace('/[^0-9]/', '', $_POST['cnic']), -4),
        'role_id' => 4, // worker
        'status' => 'active',
        'created_by' => $_SESSION['user_id']
    ]);

    // File uploads
    $filePaths = [];
    foreach (['cnic_front','cnic_back'] as $df) {
        if (isset($_FILES[$df]) && $_FILES[$df]['error'] === UPLOAD_ERR_OK) {
            $v = $upload->validate($_FILES[$df]);
            if (!$v['valid']) { $db->rollBack(); echo json_encode(['success'=>false,'message'=>$v['error']]); exit; }
            $filePaths[$df] = $upload->move($_FILES[$df], WORKER_DOCS_PATH);
        }
    }

    $workerData = array_merge($_POST, $filePaths);
    $workerId = $workerModel->add($userId, (int)$_POST['vendor_id'], $workerData);

    $audit = new AuditLog();
    $audit->log($_SESSION['user_id'], 'worker_added', 'worker', $workerId);

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Worker added.']);
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed: ' . $e->getMessage()]);
}
