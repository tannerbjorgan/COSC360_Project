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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['post_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Post ID is required'
    ]);
    exit;
}

require_once('../../Backend/config.php');

try {
    $pdo->beginTransaction();

    // Verify the post belongs to the user
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$data['post_id']]);
    $post = $stmt->fetch();

    if (!$post || $post['user_id'] != $_SESSION['user_id']) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'error' => 'Unauthorized to delete this post'
        ]);
        exit;
    }

    // Delete related comments
    $stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt->execute([$data['post_id']]);

    // Delete related likes
    $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ?");
    $stmt->execute([$data['post_id']]);

    // Delete the post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['post_id'], $_SESSION['user_id']]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Post deleted successfully'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred'
    ]);
}
?>
