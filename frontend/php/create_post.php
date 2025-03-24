<?php
session_start();

$user_id = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = isset($_POST['postTitle']) ? trim($_POST['postTitle']) : '';
    $subtitle = isset($_POST['postSubtitle']) ? trim($_POST['postSubtitle']) : '';
    $content  = isset($_POST['postContent']) ? trim($_POST['postContent']) : '';

    if (empty($title) || empty($content)) {
        echo "<p style='color:red;'>Error: Title and content are required.</p>";
        echo "<a href='../create-post.html'>Go back</a>";
        exit();
    }
    $servername = "localhost";
    $username = "root";     
    $password = "";         
    $dbname = "dfeng06"; 

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, subtitle, content, view_count, like_count, comment_count) VALUES (?, ?, ?, ?, 0, 0, 0)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("isss", $user_id, $title, $subtitle, $content);

    if ($stmt->execute()) {
        header("Location: ../user-dashboard.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../create-post.html");
    exit();
}
?>
