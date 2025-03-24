<?php
header('Content-Type: application/json');
session_start();

require_once('../../Backend/config.php');

try {
    // Simpler query first to test
    $sql = "SELECT p.*, u.username, u.name as author_name, u.id as author_id
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC";

    error_log("Executing query: " . $sql);
    
    $stmt = $pdo->query($sql);
    if (!$stmt) {
        throw new PDOException("Failed to execute query");
    }
    
    $posts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'content' => substr(strip_tags($row['content']), 0, 150) . '...',
            'author' => [
                'id' => (int)$row['author_id'],
                'name' => $row['author_name'],
                'username' => $row['username']
            ],
            'created_at' => $row['created_at'],
            'likes_count' => 0,  // Temporarily hardcoded
            'comments_count' => 0,  // Temporarily hardcoded
            'read_time' => 1  // Temporarily hardcoded
        ];
    }

    error_log("Found " . count($posts) . " posts");

    echo json_encode([
        'success' => true,
        'posts' => $posts
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_all_posts.php: " . $e->getMessage());
    error_log("Error code: " . $e->getCode());
    error_log("Error trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in get_all_posts.php: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
