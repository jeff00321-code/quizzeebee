<?php
// FIX Bug 8: session_start() MUST come before any header() or output
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', 0); // FIX: Never display raw errors to the client; log them instead
 
// FIX Bug 2: Use __DIR__ so config.php is found regardless of where the script is called from
require_once __DIR__ . '/config.php';
 
header('Content-Type: application/json');
 
// FIX Bug 11: Use if/elseif chain so only ONE action runs per request
$action = $_POST['action'] ?? $_GET['action'] ?? '';
 
if ($action === 'register') {
    try {
        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password =      $_POST['password'] ?? '';
 
        // FIX: Validate all fields server-side (never trust the client)
        if ($name === '' || $email === '' || $password === '') {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }
 
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email address"]);
            exit;
        }
 
        if (strlen($password) < 6) {
            echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
            exit;
        }
 
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT)]);
 
        echo json_encode(["status" => "success", "message" => "Account created successfully!"]);
 
    } catch (PDOException $e) {
        // FIX: Catch duplicate email (error code 23000)
        if ($e->getCode() === '23000') {
            echo json_encode(["status" => "error", "message" => "That email is already registered"]);
        } else {
            error_log($e->getMessage()); // Log real error server-side
            echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again."]);
        }
    }
 
} elseif ($action === 'login') {
    try {
        $email    = trim($_POST['email']    ?? '');
        $password =      $_POST['password'] ?? '';
 
        if ($email === '' || $password === '') {
            echo json_encode(["status" => "error", "message" => "Email and password are required"]);
            exit;
        }
 
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
 
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
 
            unset($user['password']); // Never send password hash to client
 
            echo json_encode([
                "status"   => "success",
                "user"     => $user,
                "redirect" => "dashboard.html"  // FIX Bug 5: Tell JS where to go
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect email or password"]);
        }
 
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Something went wrong. Please try again."]);
    }
 
} elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(["status" => "success"]);
 
} elseif ($action === 'add_child') {
    // FIX Bug 4: New endpoint to create a child profile
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        exit;
    }
 
    $childName = trim($_POST['child_name'] ?? '');
    $childAge  = (int)($_POST['child_age'] ?? 0);
 
    if ($childName === '' || $childAge < 1 || $childAge > 12) {
        echo json_encode(["status" => "error", "message" => "Please provide a valid name and age (1–12)"]);
        exit;
    }
 
    try {
        $stmt = $pdo->prepare("INSERT INTO child_profiles (user_id, name, age) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $childName, $childAge]);
        $childId = $pdo->lastInsertId();
        echo json_encode(["status" => "success", "child_id" => $childId, "name" => $childName]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not save child profile"]);
    }
 
} elseif ($action === 'get_children') {
    // FIX Bug 4: Fetch child profiles for the logged-in parent
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Not logged in"]);
        exit;
    }
 
    try {
        $stmt = $pdo->prepare("SELECT id, name, age FROM child_profiles WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode(["status" => "success", "children" => $stmt->fetchAll()]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not load children"]);
    }
 
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Unknown action"]);
}
?>
