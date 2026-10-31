<?php
/**
 * Database configuration & PDO connection.
 * Edit these values to match your MySQL setup (XAMPP defaults shown).
 */
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'megastore');
define('DB_USER', 'root');
define('DB_PASS', '');       // XAMPP default root password is empty
define('DB_CHARSET', 'utf8mb4');

function db() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}
