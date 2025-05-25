<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

if ($score < 0 || $score > 10) {
    echo json_encode(['success' => false, 'message' => 'Invalid score.']);
    exit();
}

try {
    require_once 'db.php';

    // Get current overall_score
    $stmt = $pdo->prepare("SELECT overall_score FROM users WHERE id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $overallScore = $row ? (int)$row['overall_score'] : 0;

    // Calculate new overall score
    $newOverallScore = $overallScore + $score;

    // Update current_score and overall_score
    $updateStmt = $pdo->prepare("UPDATE users SET current_score = :current_score, overall_score = :overall_score WHERE id = :user_id");
    $updateStmt->bindParam(':current_score', $score);
    $updateStmt->bindParam(':overall_score', $newOverallScore);
    $updateStmt->bindParam(':user_id', $user_id);

    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Scores updated successfully.',
            'currentScore' => $score,
            'overallScore' => $newOverallScore
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update scores.']);
    }
} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating scores. Please try again.']);
}
?>
