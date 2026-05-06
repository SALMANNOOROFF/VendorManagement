<?php
/**
 * Database Connection Singleton — PDO
 */
class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $host   = 'localhost';
            $dbname = 'vms_db';
            $user   = 'root';
            $pass   = '';
            $dsn    = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }
}
