<?php
require_once __DIR__ . '/../middleware/role_check.php';
// Any logged in user can use this
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../classes/Notification.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
    $action = $data['action'] ?? '';
    
    $notif = new Notification();
    if ($action === 'mark_all_read') {
        $result = $notif->markAllAsRead($_SESSION['user_id']);
        echo json_encode(['success' => $result]);
        exit;
    } elseif ($action === 'mark_read') {
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            $result = $notif->markAsRead($id);
            echo json_encode(['success' => $result]);
            exit;
        }
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
