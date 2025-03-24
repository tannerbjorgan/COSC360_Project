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
    $user_id = $_SESSION['user_id'];

    // Get counts
    $counts_sql = "
        SELECT 
            (SELECT COUNT(*) FROM followers WHERE following_id = ?) as followers_count,
            (SELECT COUNT(*) FROM followers WHERE follower_id = ?) as following_count,
            (SELECT COUNT(*) FROM posts WHERE user_id = ?) as posts_count,
            (SELECT COUNT(*) FROM likes WHERE post_id IN (SELECT id FROM posts WHERE user_id = ?)) as likes_count
    ";
    $stmt = $pdo->prepare($counts_sql);
    $stmt->execute([$user_id, $user_id, $user_id, $user_id]);
    $counts = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get recent followers
    $followers_sql = "
        SELECT u.id, u.name, u.username, u.profile_image
        FROM users u
        JOIN followers f ON u.id = f.follower_id
        WHERE f.following_id = ?
        ORDER BY f.created_at DESC
        LIMIT 5
    ";
    $stmt = $pdo->prepare($followers_sql);
    $stmt->execute([$user_id]);
    $recent_followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'analytics' => [
            'followers_count' => (int)$counts['followers_count'],
            'following_count' => (int)$counts['following_count'],
            'posts_count' => (int)$counts['posts_count'],
            'likes_count' => (int)$counts['likes_count'],
            'recent_followers' => $recent_followers
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get_user_analytics.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load analytics'
    ]);
}
?> 