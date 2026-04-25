<?php
include 'config.php';
session_start();
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'categories') {
    $stmt = $pdo->query("SELECT id, name FROM categories");
    echo json_encode(["categories" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

if ($action === 'start_quiz') {
    $cat = $_GET['cat'] ?? 0;
    $child = $_GET['child'] ?? 0;

    $stmt = $pdo->prepare("SELECT * FROM questions WHERE category_id=? ORDER BY RAND() LIMIT 5");
    $stmt->execute([$cat]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdo->prepare("INSERT INTO quiz_sessions (child_id, category_id, q1_id, q2_id, q3_id, q4_id, q5_id, score) 
                   VALUES (?,?,?,?,?,?,?,0)")
        ->execute([$child, $cat, $questions[0]['id'], $questions[1]['id'], $questions[2]['id'], $questions[3]['id'], $questions[4]['id']]);

    echo json_encode(["questions" => $questions]);
}

if ($action === 'answer') {
    $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id=?");
    $stmt->execute([$_POST['question_id']]);
    $correct = $stmt->fetchColumn();

    $isCorrect = ($_POST['answer'] === $correct);

    $stmt = $pdo->prepare("INSERT INTO session_answers (session_id, question_id, q_position, answer_emoji, is_correct) 
                           VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['session_id'], $_POST['question_id'], $_POST['position'], $_POST['answer'], $isCorrect]);

    echo json_encode(["correct" => $isCorrect]);
}

if ($action === 'finish') {
    $stmt = $pdo->prepare("UPDATE quiz_sessions SET score=? WHERE id=?");
    $stmt->execute([$_POST['score'], $_POST['session_id']]);
    echo json_encode(["status" => "success", "score" => $_POST['score']]);
}
?>
