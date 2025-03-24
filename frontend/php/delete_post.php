<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['post_id'])) {
        echo json_encode(['success' => false, 'message' => 'No post id provided.']);
        exit();
    }
    
    $post_id = intval($_POST['post_id']);
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dfeng06";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if($conn->connect_error){
        die(json_encode(['success' => false, 'message' => 'Connection failed: '.$conn->connect_error]));
    }
    
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    if(!$stmt){
        echo json_encode(['success' => false, 'message' => 'Prepare failed: '.$conn->error]);
        exit();
    }
    $stmt->bind_param("i", $post_id);
    
    if($stmt->execute()){
        echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting post: '.$stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
