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

if (!isset($_GET['keyword']) || trim($_GET['keyword']) === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Search keyword is required'
    ]);
    exit;
}

require_once('../../Backend/config.php');

try {
    $keyword = '%' . trim($_GET['keyword']) . '%';
    
    // Search in posts title and content
    $query = "SELECT p.*, 
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
        u.username as author_username,
        u.name as author_name
    FROM posts p 
    JOIN users u ON p.user_id = u.id
    WHERE (p.title LIKE :keyword OR p.content LIKE :keyword)
    ORDER BY p.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':keyword' => $keyword]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format posts data
    $formattedPosts = array_map(function($post) {
        return [
            'id' => (int)$post['id'],
            'title' => $post['title'],
            'content' => substr(strip_tags($post['content']), 0, 150) . '...',
            'created_at' => $post['created_at'],
            'likes_count' => (int)$post['likes_count'],
            'comments_count' => (int)$post['comments_count'],
            'author' => [
                'name' => $post['author_name'],
                'username' => $post['author_username']
            ],
            'can_edit' => $post['user_id'] == $_SESSION['user_id'],
            'can_delete' => $post['user_id'] == $_SESSION['user_id']
        ];
    }, $posts);

    echo json_encode([
        'success' => true,
        'posts' => $formattedPosts
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
