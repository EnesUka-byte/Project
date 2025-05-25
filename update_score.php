<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = (int)$_SESSION['user_id'];

if (!isset($_POST['score'])) {
    echo json_encode(['success' => false, 'message' => 'Score not sent in POST.']);
    exit();
}

$score = (int)$_POST['score'];

// Database connection (replace these with your actual credentials)
$host = 'localhost';
$dbname = 'hackathon_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update current_score to the new score, add to overall_score
    $stmt = $pdo->prepare("
        UPDATE users 
        SET current_score = :score, 
            overall_score = overall_score + :score 
        WHERE id = :id
    ");
    $stmt->execute([
        'score' => $score,
        'id' => $user_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Scores updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No scores updated. Maybe user not found.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
