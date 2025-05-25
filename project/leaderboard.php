<?php
session_start();
require_once 'db.php'; // Your PDO database connection

// Fetch top 10 users by overall_score (descending)
$stmt = $pdo->prepare("SELECT username, overall_score FROM users ORDER BY overall_score DESC LIMIT 10");
$stmt->execute();
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Leaderboard</title>
    <link rel="stylesheet" href="css/style.css" />
<style>
/* Leaderboard Title */
h1.leaderboard-title {
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: 'Arial', sans-serif;
}

/* Leaderboard styles */
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: separate; /* for border-radius */
    border-spacing: 0;
    font-family: 'Arial', sans-serif;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 15px; /* round the overall container */
    overflow: hidden;
}

th, td {
    padding: 15px;
    text-align: center;
    border-bottom: 1px solid #ddd; /* border only at bottom for rows */
}

th {
    background-color: #4CAF50;
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
}

/* Remove border from last row */
tbody tr:last-child td {
    border-bottom: none;
}

/* Rounded corners on the top and bottom rows only */
tbody tr:first-child td:first-child {
    border-top-left-radius: 15px;
}
tbody tr:first-child td:last-child {
    border-top-right-radius: 15px;
}
tbody tr:last-child td:first-child {
    border-bottom-left-radius: 15px;
}
tbody tr:last-child td:last-child {
    border-bottom-right-radius: 15px;
}

/* Row striping */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Hover effect */
tr:hover {
    background-color: #f1f1f1;
}

tr td {
    font-size: 1.1rem;
    color: #333;
}

tr.gold {
    background-color: #ffd700;
    color: white;
}

tr.silver {
    background-color: #c0c0c0;
    color: white;
}

tr.bronze {
    background-color: #cd7f32;
    color: white;
}

td.rank {
    font-weight: bold;
    font-size: 1.2rem;
    color: #555;
}

td.username {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}

td.score {
    font-size: 1.1rem;
    color: #4CAF50;
}

@media (max-width: 768px) {
    table {
        width: 95%;
    }
    th, td {
        font-size: 0.9rem;
        padding: 8px;
    }
}

/* Back button styling kept as before */
#backBtn {
    padding: 18px 50px;
    font-size: 1.5rem;
    font-weight: bold;
    border-radius: 30px;
    border: none;
    cursor: pointer;
    color: white;
    background: #2e7d32;
    box-shadow: 0 8px 20px rgba(46, 125, 50, 0.7);
    transition: background-color 0.3s ease;
    margin-bottom: 60px;
    user-select: none;
}

#backBtn:hover {
    background-color: #4caf50;
}

</style>

</head>
<body>

<h1 class="leaderboard-title">Leaderboard</h1>

<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Username</th>
            <th>Score</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rank = 1;
        foreach ($leaderboard as $user) {
            $rowClass = '';
            if ($rank === 1) {
                $rowClass = 'gold';
            } elseif ($rank === 2) {
                $rowClass = 'silver';
            } elseif ($rank === 3) {
                $rowClass = 'bronze';
            }

            echo "<tr class='$rowClass'>";
            echo "<td class='rank'>" . $rank . "</td>";
            echo "<td class='username'>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td class='score'>" . (int)$user['overall_score'] . "</td>";
            echo "</tr>";

            $rank++;
        }
        ?>
    </tbody>
</table>

<div style="display:flex; justify-content:center; align-items:center;">
    <a href="index.php">
        <button id="backBtn">Back</button>
    </a>
</div>

</body>
</html>
