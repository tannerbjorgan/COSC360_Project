<?php
header('Content-Type: application/json');

if (!isset($_GET['post_id'])) {
    echo json_encode(['error' => 'No post id specified.']);
    exit;
}

$post_id = intval($_GET['post_id']);
$user_id = 1;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$update = $conn->prepare("UPDATE posts SET view_count = view_count + 1 WHERE id = ?");
$update->bind_param("i", $post_id);
$update->execute();

$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Post not found.']);
    exit;
}

$post = $result->fetch_assoc();

$checkStmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $post_id, $user_id);
$checkStmt->execute();
$checkStmt->store_result();
$post['user_liked'] = ($checkStmt->num_rows > 0);
$checkStmt->close();

echo json_encode($post);
$stmt->close();
$conn->close();
$update->close();
?>
