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

// Get JSON data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['post_id']) || !isset($input['content'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Post ID and content are required'
    ]);
    exit;
}

$comment = trim($input['content']);
if (empty($comment)) {
    echo json_encode([
        'success' => false,
        'error' => 'Comment cannot be empty'
    ]);
    exit;
}

require_once('../../Backend/config.php');

try {
    $pdo->beginTransaction();

    // Verify post exists
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE id = ?");
    $stmt->execute([$input['post_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Post not found');
    }

    // Add the comment
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $input['post_id'], $comment]);
    $comment_id = $pdo->lastInsertId();

    // Get the comment details with user information
    $stmt = $pdo->prepare("
        SELECT c.*, u.username, u.name as author_name 
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'comment' => [
            'id' => $comment['id'],
            'content' => $comment['comment'],
            'created_at' => $comment['created_at'],
            'author' => [
                'name' => $comment['author_name'],
                'username' => $comment['username']
            ]
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error in add_comment.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add comment: ' . $e->getMessage()
    ]);
}
?> 