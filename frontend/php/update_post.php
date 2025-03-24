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

if (!isset($data['post_id']) || !isset($data['title']) || !isset($data['content'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields'
    ]);
    exit;
}

require_once('../../Backend/config.php');

try {
    // Verify the post belongs to the user
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$data['post_id']]);
    $post = $stmt->fetch();

    if (!$post || $post['user_id'] != $_SESSION['user_id']) {
        echo json_encode([
            'success' => false,
            'error' => 'Unauthorized to edit this post'
        ]);
        exit;
    }

    // Update the post
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([
        $data['title'],
        $data['content'],
        $data['post_id'],
        $_SESSION['user_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Post updated successfully'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred'
    ]);
}
?> 