<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance();
$stmt = $db->query("DESCRIBE entry_requests");
print_r($stmt->fetchAll());
echo "\n====\n";
$stmt2 = $db->query("DESCRIBE entry_request_workers");
print_r($stmt2->fetchAll());
