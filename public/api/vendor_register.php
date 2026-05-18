<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Vendor.php';
require_once __DIR__ . '/../../classes/FileUpload.php';
require_once __DIR__ . '/../../classes/AuditLog.php';
require_once __DIR__ . '/../../classes/FormConfig.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST only']); exit;
}

$userModel = new User();
$vendorModel = new Vendor();
$upload = new FileUpload();

// Fetch mandatory fields from DB
$fc = new FormConfig();
$configFields = $fc->getFields('vendor_registration');
$required = [];
foreach ($configFields as $f) {
    if ($f['is_mandatory']) {
        $required[] = $f['field_name'];
    }
}
// Account basics are always required if not in config
$required = array_unique(array_merge($required, ['username', 'email', 'password']));

foreach ($required as $f) {
    if (empty(trim($_POST[$f] ?? ''))) {
        // Check if it's a file
        if (!isset($_FILES[$f]) || $_FILES[$f]['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => "Field '" . ($f) . "' is required."]); exit;
        }
    }
}

// Check uniqueness
if ($userModel->emailExists($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email already registered.']); exit;
}
if ($userModel->usernameExists($_POST['username'])) {
    echo json_encode(['success' => false, 'message' => 'Username already taken.']); exit;
}

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    // 1. Create user
    $userId = $userModel->create([
        'username'  => trim($_POST['username']),
        'email'     => trim($_POST['email']),
        'password'  => $_POST['password'],
        'role_id'   => 3, // vendor role
        'status'    => 'pending',
    ]);

    // 2. Handle file uploads
    $docFields = ['registration_certificate','ntn_certificate','tax_certificate','bank_statement','company_profile_doc'];
    $filePaths = [];
    foreach ($docFields as $df) {
        if (isset($_FILES[$df]) && $_FILES[$df]['error'] === UPLOAD_ERR_OK) {
            $validation = $upload->validate($_FILES[$df]);
            if (!$validation['valid']) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => $df . ': ' . $validation['error']]); exit;
            }
            $filePaths[$df] = $upload->move($_FILES[$df], VENDOR_DOCS_PATH);
        }
    }

    // 3. Create vendor
    $vendorData = $_POST;
    foreach ($filePaths as $k => $v) { $vendorData[$k] = $v; }
    $vendorId = $vendorModel->register($userId, $vendorData);

    // 4. Create workflow
    $vendorModel->createWorkflow($userId);

    // 5. Audit log
    $audit = new AuditLog();
    $audit->log($userId, 'vendor_registered', 'vendor', $vendorId);

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Registration submitted successfully.']);

} catch (Exception $e) {
    if (isset($db)) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
}
