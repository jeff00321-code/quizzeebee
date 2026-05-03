<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'quizzybee_db');
define('DB_USER', 'root');
define('DB_PASS', '');       // leave blank — default XAMPP has no root password
define('DB_PORT', 3307);     // ✅ YOUR XAMPP MySQL runs on 3307, not the default 3306

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        "status"  => "error",
        "message" => "DB Connection failed: " . $e->getMessage()
    ]));
}
?>