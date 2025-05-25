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
<style>

body {
	
	background-color: #00bb77;
background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cpolygon fill='%23000' fill-opacity='.1' points='120 0 120 60 90 30 60 0 0 0 0 0 60 60 0 120 60 120 90 90 120 60 120 0'/%3E%3C/svg%3E");

}
/* Leaderboard Title */
h1.leaderboard-title {
    text-align: center;
    font-size: 2.8rem;
    font-weight: 900;
    color: #1b5e20; /* Dark green */
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Table container */
table {
    width: 80%;
    margin: 0 auto 50px;
    border-collapse: separate; /* for border-radius */
    border-spacing: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #ffffff; /* White background for entire table */
    box-shadow: 0 6px 15px rgba(27, 94, 32, 0.25);
    border-radius: 15px; /* Rounded corners for entire table container */
    overflow: hidden;
    table-layout: fixed;
}

/* Table headers */
th, td {
    padding: 18px 20px;
    text-align: center;
    border-bottom: 1px solid #a5d6a7; /* Light green border */
    word-break: break-word;
    font-size: 1.15rem;
    font-weight: 600;
}

/* Header row */
thead th {
    background-color: #1b5e20; /* Dark green */
    color: white;
    font-weight: 700;
    letter-spacing: 1.2px;
}

/* Rounded corners only on the table container edges */
thead th:first-child {
    border-top-left-radius: 15px;
}
thead th:last-child {
    border-top-right-radius: 15px;
}
tbody tr:last-child td:first-child {
    border-bottom-left-radius: 15px;
}
tbody tr:last-child td:last-child {
    border-bottom-right-radius: 15px;
}

/* Remove border from last row cells */
tbody tr:last-child td {
    border-bottom: none;
}

/* Default row style for non-top3 */
tbody tr:not(.gold):not(.silver):not(.bronze) {
    background-color: #ffffff;
    color: #1b5e20; /* Dark green text */
}

/* Hover effect for all rows */
tbody tr:hover:not(.gold):not(.silver):not(.bronze) {
    background-color: #c8e6c9; /* Light green on hover */
    cursor: default;
}

/* Special colors for top 3 ranks */
tr.gold {
    background: linear-gradient(135deg, #ffd700, #f5c518); /* Gold */
    color: #3a2e00;
    font-weight: 700;
}
tr.silver {
    background: linear-gradient(135deg, #c0c0c0, #a9a9a9); /* Silver */
    color: #2f2f2f;
    font-weight: 700;
}
tr.bronze {
    background: linear-gradient(135deg, #cd7f32, #b2651d); /* Bronze */
    color: #311b00;
    font-weight: 700;
}

/* Text styling */
td.rank, td.username, td.score {
    font-weight: 600;
}

/* Rank column slightly bigger font */
td.rank {
    font-size: 1.25rem;
}

/* Back button styling */
#backBtn {
    padding: 16px 60px;
    font-size: 1.4rem;
    font-weight: 700;
    border-radius: 40px;
    border: none;
    cursor: pointer;
    color: white;
    background: linear-gradient(135deg, #1b5e20, #4caf50);
    box-shadow: 0 6px 18px rgba(27, 94, 32, 0.8);
    transition: background-color 0.3s ease, transform 0.2s ease;
    margin: 20px auto 60px;
    user-select: none;
    display: block;
    max-width: 280px;
    width: 100%;
    letter-spacing: 1.1px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

#backBtn:hover {
    background: linear-gradient(135deg, #4caf50, #1b5e20);
    transform: scale(1.05);
    box-shadow: 0 8px 22px rgba(46, 125, 50, 1);
}

/* Responsive */
@media (max-width: 768px) {
    table {
        width: 95%;
    }
    th, td {
        font-size: 1rem;
        padding: 12px 10px;
    }
    h1.leaderboard-title {
        font-size: 2rem;
        margin-bottom: 20px;
    }
    #backBtn {
        font-size: 1.2rem;
        padding: 14px 40px;
        max-width: 100%;
    }
}

@media (max-width: 400px) {
    th, td {
        font-size: 0.9rem;
        padding: 8px 6px;
    }
    #backBtn {
        padding: 12px 30px;
        font-size: 1rem;
    }
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
    <a style="  text-decoration: none;" href="index.php" aria-label="Back to homepage">
        <button style="text-decoration: none;" id="backBtn">Back</button>
    </a>
</div>

</body>
</html>
