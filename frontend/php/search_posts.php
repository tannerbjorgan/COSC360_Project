<?php
header('Content-Type: application/json');

if (!isset($_GET['keyword'])) {
    echo json_encode([]);
    exit;
}

$keyword = $_GET['keyword'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE title LIKE CONCAT('%', ?, '%') ORDER BY created_at DESC");
$stmt->bind_param("s", $keyword);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($posts);
?>
