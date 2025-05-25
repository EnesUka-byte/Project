<?php
// save_score.php

// Get the JSON data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data contains all the necessary values
if (isset($data['user_id'], $data['score'], $data['total_score'])) {
    $userId = $data['user_id'];
    $score = $data['score'];
    $totalScore = $data['total_score'];

    // Database connection
    require 'db.php'; // Include your existing database connection

    try {
        // Update the user's score in the 'users' table
        $sql = "UPDATE users SET score = :score, total_score = :total_score WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters and execute
        $stmt->execute([
            ':score' => $score,
            ':total_score' => $totalScore,
            ':user_id' => $userId
        ]);

        // Return a success response
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Return an error response if something goes wrong
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // Return an error response if the required data is missing
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}
?>
