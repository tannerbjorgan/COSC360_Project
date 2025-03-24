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
    
    // Get user stats
    $statsQuery = "SELECT 
        (SELECT COUNT(*) FROM posts WHERE user_id = :user_id) as total_posts,
        (SELECT COUNT(*) FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = :user_id)) as total_likes,
        (SELECT COUNT(*) FROM comments WHERE post_id IN (SELECT id FROM posts WHERE user_id = :user_id)) as total_comments,
        (SELECT COUNT(*) FROM followers WHERE followed_id = :user_id) as total_followers";

    $stmt = $pdo->prepare($statsQuery);
    $stmt->execute([':user_id' => $userId]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_posts' => (int)$stats['total_posts'],
            'total_likes' => (int)$stats['total_likes'],
            'total_comments' => (int)$stats['total_comments'],
            'total_followers' => (int)$stats['total_followers']
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_user_stats.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log("General error in get_user_stats.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred'
    ]);
}
?>
