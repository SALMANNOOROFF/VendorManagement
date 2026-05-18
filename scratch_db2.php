<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();
$stmt = $db->query("DESCRIBE entry_requests");
foreach($stmt->fetchAll() as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
