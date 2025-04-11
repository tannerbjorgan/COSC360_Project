<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error'   => 'User not authenticated'
    ]);
    exit;
}

require_once('../../Backend/config.php');  // This file should create a PDO connection in $pdo

try {
    // Begin a transaction
    $pdo->beginTransaction();

    // Delete user's profile image if it exists
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['profile_image']) {
        $image_path = '../uploads/' . $user['profile_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Delete user's comments
    $stmt = $pdo->prepare("DELETE FROM comments WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Delete user's likes
    $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Delete user's followers/following relationships
    // Note: Adjust the column names as per your schema.
    $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? OR following_id = ?");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);

    // Delete user's posts
    $stmt = $pdo->prepare("DELETE FROM posts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Finally, delete the user record
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$_SESSION['user_id']])) {
        $pdo->commit();
        session_destroy();
        echo json_encode([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete account');
    }
    
} catch (Exception $e) {
    // Roll back the transaction on error
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
