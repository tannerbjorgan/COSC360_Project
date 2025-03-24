<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$post_id = intval($_POST['post_id']);
$user_id = 1; 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$checkStmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $post_id, $user_id);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows > 0) {
    $stmt2 = $conn->prepare("SELECT like_count FROM posts WHERE id = ?");
    $stmt2->bind_param("i", $post_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'like_count' => $row['like_count'], 'message' => 'Already liked']);
    $stmt2->close();
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

$insertStmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
$insertStmt->bind_param("ii", $post_id, $user_id);
if (!$insertStmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $insertStmt->error]);
    exit;
}
$insertStmt->close();

$updateStmt = $conn->prepare("UPDATE posts SET like_count = like_count + 1 WHERE id = ?");
$updateStmt->bind_param("i", $post_id);
if ($updateStmt->execute()) {
    $stmt3 = $conn->prepare("SELECT like_count FROM posts WHERE id = ?");
    $stmt3->bind_param("i", $post_id);
    $stmt3->execute();
    $result = $stmt3->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'like_count' => $row['like_count']]);
    $stmt3->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $updateStmt->error]);
}
$updateStmt->close();
$conn->close();
?>
