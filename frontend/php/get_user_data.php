<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated'
    ]);
    exit;
}

require_once('../../Backend/db_connection.php');

try {
    $stmt = $conn->prepare("
        SELECT u.username, u.email, u.profile_image,
               (SELECT COUNT(*) FROM followers WHERE followed_id = u.id) as followers_count
        FROM users u
        WHERE u.id = ?
    ");
    
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // If profile image exists, create the full URL
        if ($user['profile_image']) {
            $user['profile_image'] = '../uploads/' . $user['profile_image'];
        }
        
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'User not found'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 