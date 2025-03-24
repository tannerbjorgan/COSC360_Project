<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$user_id = 1;
$sql = "SELECT posts.*, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.user_id = $user_id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);

$posts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

echo json_encode($posts);
$conn->close();
?>
