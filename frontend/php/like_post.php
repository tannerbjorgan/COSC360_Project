<?php
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$post_id = intval($_POST['post_id']);
$user_id = 1; // Replace with a dynamic user ID (for example, from $_SESSION) as needed

require_once('../../Backend/config.php'); // This file should create a PDO connection in $pdo

try {
    // Check if the user already liked the post
    $checkSQL = "SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id";
    $stmt = $pdo->prepare($checkSQL);
    $stmt->execute([
        'post_id' => $post_id,
        'user_id' => $user_id
    ]);
    
    if ($stmt->rowCount() > 0) {
        // If already liked, get the current like count from posts
        $stmt2 = $pdo->prepare("SELECT like_count FROM posts WHERE id = :post_id");
        $stmt2->execute(['post_id' => $post_id]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'success'    => true,
            'like_count' => $row ? (int)$row['like_count'] : 0,
            'message'    => 'Already liked'
        ]);
        exit;
    }
    
    // Insert a new like record
    $insertSQL = "INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)";
    $stmtInsert = $pdo->prepare($insertSQL);
    $stmtInsert->execute([
        'post_id' => $post_id,
        'user_id' => $user_id
    ]);
    
    // Update the like count in posts by incrementing it by 1
    $updateSQL = "UPDATE posts SET like_count = like_count + 1 WHERE id = :post_id";
    $stmtUpdate = $pdo->prepare($updateSQL);
    $stmtUpdate->execute(['post_id' => $post_id]);
    
    // Retrieve the new like count
    $stmt3 = $pdo->prepare("SELECT like_count FROM posts WHERE id = :post_id");
    $stmt3->execute(['post_id' => $post_id]);
    $row = $stmt3->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success'    => true,
        'like_count' => $row ? (int)$row['like_count'] : 0
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
