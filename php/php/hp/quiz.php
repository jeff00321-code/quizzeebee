<?php
include 'config.php';
session_start();

if ($_GET['action'] === 'categories') {
    $stmt = $pdo->query("SELECT * FROM categories");
    echo json_encode(["categories" => $stmt->fetchAll()]);
}

if ($_GET['action'] === 'start_quiz') {
    $cat = $_GET['cat'];
    $child = $_GET['child'];

    $stmt = $pdo->prepare("SELECT * FROM questions WHERE category_id=? ORDER BY RAND() LIMIT 5");
    $stmt->execute([$cat]);
    $questions = $stmt->fetchAll();

    $pdo->prepare("INSERT INTO quiz_sessions (child_id, category_id, q1_id, q2_id, q3_id, q4_id, q5_id, score) 
                   VALUES (?,?,?,?,?,?,?,0)")
        ->execute([$child, $cat, $questions[0]['id'], $questions[1]['id'], $questions[2]['id'], $questions[3]['id'], $questions[4]['id']]);

    echo json_encode(["questions" => $questions]);
}

if ($_POST['action'] === 'answer') {
    $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id=?");
    $stmt->execute([$_POST['question_id']]);
    $correct = $stmt->fetchColumn();

    $isCorrect = ($_POST['answer'] === $correct);

    $stmt = $pdo->prepare("INSERT INTO session_answers (session_id, question_id, q_position, answer_emoji, is_correct) 
                           VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['session_id'], $_POST['question_id'], $_POST['position'], $_POST['answer'], $isCorrect]);

    echo json_encode(["correct" => $isCorrect]);
}

if ($_POST['action'] === 'finish') {
    $stmt = $pdo->prepare("UPDATE quiz_sessions SET score=? WHERE id=?");
    $stmt->execute([$_POST['score'], $_POST['session_id']]);
    echo json_encode(["ok" => true, "score" => $_POST['score']]);
}
?>
