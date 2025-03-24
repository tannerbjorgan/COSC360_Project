<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not authenticated']));
}

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['post_id'])) {
    die(json_encode(['error' => 'Post ID is required']));
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$input['post_id'];

// Check if the user has already liked the post
$check_sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $post_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Unlike the post
    $delete_sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $user_id, $post_id);
    $delete_stmt->execute();
} else {
    // Like the post
    $insert_sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $user_id, $post_id);
    $insert_stmt->execute();
}

// Get updated like count
$count_sql = "SELECT COUNT(*) as likes_count FROM likes WHERE post_id = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $post_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$likes_count = $count_result->fetch_assoc()['likes_count'];

echo json_encode([
    'success' => true,
    'likes_count' => (int)$likes_count
]);

$conn->close();
?> 