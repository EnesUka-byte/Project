<?php
$host = 'localhost';
$db   = 'hackathon_db';
$user = 'root'; // your MySQL username
$pass = '';     // your MySQL password, usually empty for XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,  // Use native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // You can customize the error message or log it instead of showing it directly in production
    exit('Database connection failed: ' . $e->getMessage());
}
?>
