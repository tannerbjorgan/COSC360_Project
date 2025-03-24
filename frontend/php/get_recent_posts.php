<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated'
    ]);
    exit;
}

require_once('../../Backend/config.php');

try {
    $userId = $_SESSION['user_id'];
    
    // Get recent posts with likes and comments count
    $query = "SELECT p.*, 
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
        p.user_id = :user_id as can_edit,
        p.user_id = :user_id as can_delete
    FROM posts p 
    WHERE p.user_id = :user_id 
    ORDER BY p.created_at DESC 
    LIMIT 5";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $userId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format posts data
    $formattedPosts = array_map(function($post) {
        return [
            'id' => (int)$post['id'],
            'title' => $post['title'],
            'content' => substr(strip_tags($post['content']), 0, 150) . '...',
            'created_at' => $post['created_at'],
            'likes_count' => (int)$post['likes_count'],
            'comments_count' => (int)$post['comments_count'],
            'can_edit' => (bool)$post['can_edit'],
            'can_delete' => (bool)$post['can_delete']
        ];
    }, $posts);

    echo json_encode([
        'success' => true,
        'posts' => $formattedPosts
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_recent_posts.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("General error in get_recent_posts.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred'
    ]);
}
?>
