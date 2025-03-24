<?php
header('Content-Type: application/json');
session_start();

require_once('../../Backend/config.php');

try {
    // Get all categories
    $stmt = $pdo->query("SELECT DISTINCT category FROM posts WHERE category IS NOT NULL ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);

} catch (Exception $e) {
    error_log("Error in get_categories.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load categories'
    ]);
}
?> 