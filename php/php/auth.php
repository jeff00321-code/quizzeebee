<?php
include 'config.php';
session_start();

if ($_POST['action'] === 'register') {
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->execute([$_POST['name'], $_POST['email'], password_hash($_POST['password'], PASSWORD_BCRYPT)]);
    echo json_encode(["ok" => true]);
}

if ($_POST['action'] === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(["ok" => true, "user" => $user]);
    } else {
        echo json_encode(["ok" => false, "error" => "Invalid credentials"]);
    }
}

if ($_POST['action'] === 'logout') {
    session_destroy();
    echo json_encode(["ok" => true]);
}
?>
