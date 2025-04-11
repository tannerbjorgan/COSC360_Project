<?php
session_start();
require_once 'config.php';

// Ensure user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Don't allow deleting your own account. This is to prevent the admin deleting their own account
if ($userId === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
    exit;
}

try {

    // Start transaction sequence of deleting user's posts, comments, likes, followers, and category preferences
    $pdo->beginTransaction();

    // Delete user's posts
    $stmt = $pdo->prepare("DELETE FROM posts WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user's comments
    $stmt = $pdo->prepare("DELETE FROM comments WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user's likes
    $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user's followers/following relationships
    $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? OR following_id = ?");
    $stmt->execute([$userId, $userId]);

    // Delete user's category preferences
    $stmt = $pdo->prepare("DELETE FROM user_categories WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Finally, delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Rollback on error
    $pdo->rollBack();
    error_log("Error deleting user: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?> 