<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();
    $sql = file_get_contents(__DIR__ . '/entry_requests.sql');
    
    // Split by semicolon and execute each part
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        $db->exec($statement);
    }
    
    echo "Migration successful!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
