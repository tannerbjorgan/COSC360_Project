<?php
header('Content-Type: application/json');
session_start();

// Check if the user is authenticated.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Read JSON input from the request body.
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['post_id'])) {
    echo json_encode(['error' => 'Post ID is required']);
    exit;
}

$post_id = (int)$input['post_id'];
$user_id = $_SESSION['user_id'];

require_once('../../Backend/config.php'); // This should create a PDO connection in $pdo.

try {
    // Check if the user already liked the post.
    $checkSQL = "SELECT id FROM likes WHERE user_id = :user_id AND post_id = :post_id";
    $stmt = $pdo->prepare($checkSQL);
    $stmt->execute([
        'user_id' => $user_id,
        'post_id' => $post_id
    ]);

    if ($stmt->rowCount() > 0) {
        // If the like exists, remove it (unlike).
        $deleteSQL = "DELETE FROM likes WHERE user_id = :user_id AND post_id = :post_id";
        $deleteStmt = $pdo->prepare($deleteSQL);
        $deleteStmt->execute([
            'user_id' => $user_id,
            'post_id' => $post_id
        ]);
    } else {
        // Otherwise, add a new like.
        $insertSQL = "INSERT INTO likes (user_id, post_id) VALUES (:user_id, :post_id)";
        $insertStmt = $pdo->prepare($insertSQL);
        $insertStmt->execute([
            'user_id' => $user_id,
            'post_id' => $post_id
        ]);
    }
    
    // Get the updated like count.
    $countSQL = "SELECT COUNT(*) as likes_count FROM likes WHERE post_id = :post_id";
    $countStmt = $pdo->prepare($countSQL);
    $countStmt->execute(['post_id' => $post_id]);
    $row = $countStmt->fetch(PDO::FETCH_ASSOC);
    $likes_count = $row ? (int)$row['likes_count'] : 0;
    
    echo json_encode([
        'success' => true,
        'likes_count' => $likes_count
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
