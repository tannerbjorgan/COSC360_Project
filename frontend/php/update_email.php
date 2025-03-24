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
$email = isset($data['email']) ? trim($data['email']) : '';

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid email address'
    ]);
    exit;
}

require_once('../../Backend/db_connection.php');

try {
    // Check if email is already in use by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Email address is already in use'
        ]);
        exit;
    }
    
    // Update email
    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $email, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Email updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update email');
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