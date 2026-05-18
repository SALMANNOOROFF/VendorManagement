<?php
require_once __DIR__ . '/../config/database.php';
try {
    $db = Database::getInstance();
    $db->exec("ALTER TABLE `entry_requests` MODIFY COLUMN `status` ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft'");
    echo "Success";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
