<?php
header('Content-Type: application/json');

$user_id = 1;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

$stmt = $conn->prepare("SELECT COALESCE(SUM(view_count),0) AS total_views, 
                               COALESCE(SUM(like_count),0) AS total_likes, 
                               COALESCE(SUM(comment_count),0) AS total_comments
                        FROM posts
                        WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();
$stmt->close();

$stmt2 = $conn->prepare("SELECT COUNT(*) AS total_followers FROM followers WHERE following_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$followers = $result2->fetch_assoc();
$stmt2->close();

$conn->close();

$userStats = array_merge($stats, $followers);

echo json_encode([
    'totalViews' => $userStats['total_views'],
    'totalLikes' => $userStats['total_likes'],
    'totalComments' => $userStats['total_comments'],
    'totalFollowers' => $userStats['total_followers']
]);
?>
