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
    // Get users that the current user is following
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.username, u.profile_image
        FROM users u
        JOIN followers f ON u.id = f.following_id
        WHERE f.follower_id = ?
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $following = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'following' => $following
    ]);

} catch (Exception $e) {
    error_log("Error in get_following.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load following list'
    ]);
}
?> 