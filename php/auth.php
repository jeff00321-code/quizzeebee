<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'register') {
    try {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
            echo json_encode(["status" => "error", "message" => "All fields required"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_BCRYPT)]);
        echo json_encode(["status" => "success"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

if ($action === 'login') {
    try {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            echo json_encode(["status" => "error", "message" => "Email and password required"]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            unset($user['password']);
            echo json_encode(["status" => "success", "user" => $user]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(["status" => "success"]);
}
?>
