<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'quizzybee_db');
define('DB_USER', 'root');   // default XAMPP user
define('DB_PASS', '');       // leave blank unless you set a password

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>
