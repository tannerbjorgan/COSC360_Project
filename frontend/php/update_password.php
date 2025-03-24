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

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$old_password = isset($data['old_password']) ? $data['old_password'] : '';
$new_password = isset($data['new_password']) ? $data['new_password'] : '';

if (!$old_password || !$new_password) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields'
    ]);
    exit;
}

if (strlen($new_password) < 6) {
    echo json_encode([
        'success' => false,
        'error' => 'New password must be at least 6 characters long'
    ]);
    exit;
}

require_once('../../Backend/db_connection.php');

try {
    // Verify old password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user || !password_verify($old_password, $user['password'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Current password is incorrect'
        ]);
        exit;
    }
    
    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update password');
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