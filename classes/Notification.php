<?php
require_once __DIR__ . '/../config/database.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($userId, $title, $message, $link = null) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $title, $message, $link]);
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'unread'");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['count'] : 0;
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notifications SET status = 'read' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET status = 'read' WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
?>
