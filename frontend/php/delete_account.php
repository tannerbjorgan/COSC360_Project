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

require_once('../../Backend/db_connection.php');

try {
    $conn->begin_transaction();
    
    // Delete user's profile image if it exists
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['profile_image']) {
        $image_path = '../uploads/' . $user['profile_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete user's comments
    $stmt = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    
    // Delete user's likes
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    
    // Delete user's followers/following relationships
    $stmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? OR followed_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    
    // Delete user's posts
    $stmt = $conn->prepare("DELETE FROM posts WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    
    // Finally, delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $conn->commit();
        session_destroy();
        echo json_encode([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete account');
    }
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 