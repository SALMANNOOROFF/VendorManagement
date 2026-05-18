<?php
require 'config/database.php';
$db = Database::getInstance();
$db->query('ALTER TABLE entry_requests ADD COLUMN remarks TEXT DEFAULT NULL AFTER status');
echo "Migration successful\n";
