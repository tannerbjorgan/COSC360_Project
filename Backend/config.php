<?php
$host   = 'localhost';
$dbName = 'dfeng06'; 
$dbUser = 'root';    // Changed to default XAMPP username
$dbPass = '';        // Changed to default XAMPP password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
