<?php
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'Post ID is required']));
}

$post_id = (int)$_GET['id'];

// Get post details with author information and stats
$sql = "SELECT p.*, u.username, u.name as author_name,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count,
        IF(EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?), 1, 0) as user_liked
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(['error' => 'Post not found']));
}

$post = $result->fetch_assoc();

// Get comments for the post
$comments_sql = "SELECT c.*, u.username, u.name as author_name 
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at DESC";

$stmt = $conn->prepare($comments_sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments_result = $stmt->get_result();

$comments = [];
while ($comment = $comments_result->fetch_assoc()) {
    $comments[] = [
        'id' => $comment['id'],
        'content' => $comment['comment'],
        'created_at' => $comment['created_at'],
        'author' => [
            'name' => $comment['author_name'],
            'username' => $comment['username']
        ]
    ];
}

// Format the response
$response = [
    'success' => true,
    'post' => [
        'id' => $post['id'],
        'title' => $post['title'],
        'content' => $post['content'],
        'created_at' => $post['created_at'],
        'author' => [
            'name' => $post['author_name'],
            'username' => $post['username'],
            'id' => $post['user_id']
        ],
        'likes_count' => (int)$post['likes_count'],
        'comments_count' => (int)$post['comments_count'],
        'user_liked' => (bool)$post['user_liked'],
        'comments' => $comments
    ]
];

echo json_encode($response);
$conn->close();
?>
