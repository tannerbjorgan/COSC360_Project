<?php
$host   = 'localhost';
$dbName = 'dfeng06'; 
$dbUser = 'dfeng06';    
$dbPass = 'dfeng06';        

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
