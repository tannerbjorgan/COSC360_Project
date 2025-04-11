<?php
header('Content-Type: application/json');
session_start();

require_once('../../Backend/config.php');

try {
    // Query to get posts along with their authors and aggregated counts.
    $sql = "
        SELECT p.*, 
               u.username, 
               u.name AS author_name, 
               u.id AS author_id,
               (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS likes_count,
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comments_count
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC
    ";

    error_log("Executing query: " . $sql);
    $stmt = $pdo->query($sql);
    if (!$stmt) {
        throw new PDOException("Failed to execute query");
    }
    
    $posts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Calculate a simple read time estimate (words per minute = 200)
        $wordCount = str_word_count(strip_tags($row['content']));
        $readTime = ceil($wordCount / 200);
        if ($readTime < 1) {
            $readTime = 1;
        }
        
        $posts[] = [
            'id'             => (int)$row['id'],
            'title'          => $row['title'],
            'content'        => substr(strip_tags($row['content']), 0, 150) . '...',
            'author'         => [
                'id'       => (int)$row['author_id'],
                'name'     => $row['author_name'],
                'username' => $row['username']
            ],
            'created_at'     => $row['created_at'],
            'likes_count'    => (int)$row['likes_count'],
            'comments_count' => (int)$row['comments_count'],
            'read_time'      => $readTime
        ];
    }

    error_log("Found " . count($posts) . " posts");

    echo json_encode([
        'success' => true,
        'posts'   => $posts
    ]);

} catch (PDOException $e) {
    error_log("Database error in get_all_posts.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error'   => 'Database error occurred: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in get_all_posts.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error'   => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
