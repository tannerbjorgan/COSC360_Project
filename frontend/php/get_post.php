<?php
header('Content-Type: application/json');
session_start();

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Post ID is required']);
    exit;
}

$post_id = (int)$_GET['id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

require_once('../../Backend/config.php'); // This file sets up $pdo as a PDO connection

try {
    // Query post details along with author info and stats.
    // Note: We now include u.profile_image in the SELECT list.
    $sql = "
        SELECT p.*, 
               u.username, 
               u.name as author_name,
               u.profile_image,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
               IF(
                 EXISTS(
                   SELECT 1 FROM likes WHERE post_id = p.id AND user_id = :user_id
                 ),
                 1,
                 0
               ) as user_liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = :post_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'post_id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo json_encode(['error' => 'Post not found']);
        exit;
    }

    // Query to get comments for the post.
    $comments_sql = "
        SELECT c.*, u.username, u.name as author_name, u.profile_image
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = :post_id
        ORDER BY c.created_at DESC
    ";
    $stmt = $pdo->prepare($comments_sql);
    $stmt->execute(['post_id' => $post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format comments appropriately.
    $formatted_comments = [];
    foreach ($comments as $comment) {
        $formatted_comments[] = [
            'id'         => $comment['id'],
            'content'    => $comment['comment'],
            'created_at' => $comment['created_at'],
            'author'     => [
                'name'          => $comment['author_name'],
                'username'      => $comment['username'],
                'profile_image' => $comment['profile_image']
            ]
        ];
    }

    // Format the final response including the profile_image for the author.
    $response = [
        'success' => true,
        'post'    => [
            'id'             => $post['id'],
            'title'          => $post['title'],
            'content'        => $post['content'],
            'created_at'     => $post['created_at'],
            'author'         => [
                'name'          => $post['author_name'],
                'username'      => $post['username'],
                'id'            => $post['user_id'],
                'profile_image' => $post['profile_image']
            ],
            'likes_count'    => (int)$post['likes_count'],
            'comments_count' => (int)$post['comments_count'],
            'user_liked'     => (bool)$post['user_liked'],
            'comments'       => $formatted_comments
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
