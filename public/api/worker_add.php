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

require_once __DIR__ . '/../../classes/FormConfig.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']); exit;
}
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'vendor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
}

// Dynamic validation config
$fc = new FormConfig();
$configFields = $fc->getFields('worker_registration');
$mandatoryFields = [];
foreach ($configFields as $f) {
    if ($f['is_mandatory']) {
        $mandatoryFields[] = $f['field_name'];
    }
}
$essentialFields = array_unique(array_merge($mandatoryFields, ['first_name', 'cnic']));

$workerModel = new Worker();
$userModel = new User();
$upload = new FileUpload();
$audit = new AuditLog();

$workersData = $_POST['workers'] ?? [];
$vendorId = (int)($_POST['vendor_id'] ?? 0);

if (empty($workersData) || !$vendorId) {
    echo json_encode(['success' => false, 'message' => 'No worker data or vendor ID provided.']); exit;
}

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    foreach ($workersData as $idx => $data) {
        // 1. Validate essentials for this worker
        foreach ($essentialFields as $f) {
            if (empty(trim($data[$f] ?? ''))) {
                // Check files too
                $fileError = $_FILES['workers']['error'][$idx][$f] ?? UPLOAD_ERR_NO_FILE;
                if ($fileError !== UPLOAD_ERR_OK) {
                    throw new Exception("Worker #".($idx+1).": Field '{$f}' is required.");
                }
            }
        }

        // 2. Check CNIC duplicate
        if ($workerModel->cnicExists($data['cnic'])) {
            throw new Exception("Worker #".($idx+1).": CNIC '{$data['cnic']}' is already registered.");
        }

        // 3. Create User
        $workerUsername = 'w_' . preg_replace('/[^0-9]/', '', $data['cnic']);
        $workerEmail = $workerUsername . '@worker.vms.local';
        $userId = $userModel->create([
            'username' => $workerUsername,
            'email' => $workerEmail,
            'password' => 'Worker@' . substr(preg_replace('/[^0-9]/', '', $data['cnic']), -4),
            'role_id' => 4, // worker
            'status' => 'active',
            'created_by' => $_SESSION['user_id']
        ]);

        // 4. Handle Files for this worker
        $filePaths = [];
        if (isset($_FILES['workers']['name'][$idx])) {
            foreach ($_FILES['workers']['name'][$idx] as $fieldName => $fileName) {
                if ($_FILES['workers']['error'][$idx][$fieldName] === UPLOAD_ERR_OK) {
                    // Normalize file array for FileUpload class
                    $fileObj = [
                        'name' => $_FILES['workers']['name'][$idx][$fieldName],
                        'type' => $_FILES['workers']['type'][$idx][$fieldName],
                        'tmp_name' => $_FILES['workers']['tmp_name'][$idx][$fieldName],
                        'error' => $_FILES['workers']['error'][$idx][$fieldName],
                        'size' => $_FILES['workers']['size'][$idx][$fieldName]
                    ];
                    $v = $upload->validate($fileObj);
                    if (!$v['valid']) throw new Exception("Worker #".($idx+1)." ({$fieldName}): " . $v['error']);
                    $filePaths[$fieldName] = $upload->move($fileObj, WORKER_DOCS_PATH);
                }
            }
        }

        // 5. Save Worker
        $defaults = [
            'first_name' => '', 'last_name' => '', 'cnic' => '', 'phone' => '', 
            'designation' => '', 'join_date' => date('Y-m-d'),
            'date_of_birth' => null, 'gender' => null, 'email' => null, 'department' => null
        ];
        $finalData = array_merge($defaults, $data, $filePaths);
        $workerId = $workerModel->add($userId, $vendorId, $finalData);

        $audit->log($_SESSION['user_id'], 'worker_bulk_added', 'worker', $workerId);
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => count($workersData) . ' workers registered successfully.']);
} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
