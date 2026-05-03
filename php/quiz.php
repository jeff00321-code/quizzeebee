<?php
// FIX Bug 8: session_start() first, always
session_start();
 
error_reporting(E_ALL);
ini_set('display_errors', 0);
 
// FIX Bug 2: Use __DIR__ for reliable path resolution
require_once __DIR__ . '/config.php';
 
header('Content-Type: application/json');
 
// FIX Bug 7: Auth check helper - call this on any action that needs a logged-in user
function requireAuth() {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Please log in first"]);
        exit;
    }
}
 
// FIX Bug 11: Use if/elseif chain so only ONE block runs
$action = $_GET['action'] ?? $_POST['action'] ?? '';
 
if ($action === 'categories') {
    // Categories are public (landing page uses them)
    try {
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
        echo json_encode(["categories" => $stmt->fetchAll()]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not load categories"]);
    }
 
} elseif ($action === 'start_quiz') {
    requireAuth(); // FIX Bug 7
 
    // FIX Bug 9: Cast inputs to int and validate
    $cat   = (int)($_GET['cat']   ?? 0);
    $child = (int)($_GET['child'] ?? 0);
 
    if ($cat <= 0 || $child <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid category or child"]);
        exit;
    }
 
    try {
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE category_id = ? ORDER BY RAND() LIMIT 5");
        $stmt->execute([$cat]);
        $questions = $stmt->fetchAll();
 
        // FIX Bug 3: Check that we actually got 5 questions before accessing by index
        if (count($questions) < 5) {
            echo json_encode(["status" => "error", "message" => "Not enough questions in this category yet. Please add more!"]);
            exit;
        }
 
        $pdo->prepare(
            "INSERT INTO quiz_sessions (child_id, category_id, q1_id, q2_id, q3_id, q4_id, q5_id, score)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0)"
        )->execute([
            $child, $cat,
            $questions[0]['id'], $questions[1]['id'], $questions[2]['id'],
            $questions[3]['id'], $questions[4]['id']
        ]);
 
        $sessionId = $pdo->lastInsertId();
 
        // Don't send correct_answer to the client! Strip it out.
        $safeQuestions = array_map(function($q) {
            return [
                'id'            => $q['id'],
                'question_text' => $q['question_text'],
                'option1'       => $q['option1'],
                'option2'       => $q['option2'],
                'option3'       => $q['option3'],
                'option4'       => $q['option4'],
            ];
        }, $questions);
 
        echo json_encode(["status" => "success", "session_id" => $sessionId, "questions" => $safeQuestions]);
 
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not start quiz"]);
    }
 
} elseif ($action === 'answer') {
    requireAuth(); // FIX Bug 7
 
    // FIX Bug 9: Validate and cast inputs
    $questionId = (int)($_POST['question_id'] ?? 0);
    $sessionId  = (int)($_POST['session_id']  ?? 0);
    $position   = (int)($_POST['position']    ?? 0);
    $answer     = trim($_POST['answer']       ?? '');
 
    if ($questionId <= 0 || $sessionId <= 0 || $answer === '') {
        echo json_encode(["status" => "error", "message" => "Invalid answer submission"]);
        exit;
    }
 
    try {
        $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id = ?");
        $stmt->execute([$questionId]);
        $correct = $stmt->fetchColumn();
 
        if ($correct === false) {
            echo json_encode(["status" => "error", "message" => "Question not found"]);
            exit;
        }
 
        $isCorrect = ($answer === $correct) ? 1 : 0;
 
        $stmt = $pdo->prepare(
            "INSERT INTO session_answers (session_id, question_id, q_position, answer_emoji, is_correct)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$sessionId, $questionId, $position, $answer, $isCorrect]);
 
        echo json_encode(["correct" => (bool)$isCorrect, "correct_answer" => $correct]);
 
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not record answer"]);
    }
 
} elseif ($action === 'finish') {
    requireAuth(); // FIX Bug 7
 
    // FIX Bug 9: Validate inputs
    $score     = (int)($_POST['score']      ?? 0);
    $sessionId = (int)($_POST['session_id'] ?? 0);
 
    if ($sessionId <= 0 || $score < 0 || $score > 5) {
        echo json_encode(["status" => "error", "message" => "Invalid finish data"]);
        exit;
    }
 
    try {
        $stmt = $pdo->prepare("UPDATE quiz_sessions SET score = ? WHERE id = ?");
        $stmt->execute([$score, $sessionId]);
        echo json_encode(["status" => "success", "score" => $score]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not save score"]);
    }
 
} elseif ($action === 'history') {
    requireAuth();
 
    $childId = (int)($_GET['child_id'] ?? 0);
    if ($childId <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid child"]);
        exit;
    }
 
    try {
        $stmt = $pdo->prepare(
            "SELECT qs.id, qs.score, qs.created_at, c.name AS category
             FROM quiz_sessions qs
             JOIN categories c ON c.id = qs.category_id
             WHERE qs.child_id = ?
             ORDER BY qs.created_at DESC
             LIMIT 20"
        );
        $stmt->execute([$childId]);
        echo json_encode(["status" => "success", "sessions" => $stmt->fetchAll()]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(["status" => "error", "message" => "Could not load history"]);
    }
 
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Unknown action"]);
}
?>