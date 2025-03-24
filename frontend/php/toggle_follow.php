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

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User ID is required'
    ]);
    exit;
}

require_once('../../Backend/config.php');

try {
    $follower_id = $_SESSION['user_id'];
    $following_id = $input['user_id'];

    // Don't allow users to follow themselves
    if ($follower_id == $following_id) {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot follow yourself'
        ]);
        exit;
    }

    // Check if already following
    $stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$follower_id, $following_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Unfollow
        $stmt = $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?");
        $stmt->execute([$follower_id, $following_id]);
        $is_following = false;
    } else {
        // Follow
        $stmt = $pdo->prepare("INSERT INTO followers (follower_id, following_id) VALUES (?, ?)");
        $stmt->execute([$follower_id, $following_id]);
        $is_following = true;
    }

    echo json_encode([
        'success' => true,
        'is_following' => $is_following
    ]);

} catch (Exception $e) {
    error_log("Error in toggle_follow.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update follow status'
    ]);
}
?> 