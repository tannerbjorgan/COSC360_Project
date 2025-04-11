<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error'   => 'User not authenticated'
    ]);
    exit;
}

// Get JSON data from the request body.
$data = json_decode(file_get_contents('php://input'), true);
$old_password = isset($data['old_password']) ? $data['old_password'] : '';
$new_password = isset($data['new_password']) ? $data['new_password'] : '';

if (!$old_password || !$new_password) {
    echo json_encode([
        'success' => false,
        'error'   => 'Missing required fields'
    ]);
    exit;
}

if (strlen($new_password) < 6) {
    echo json_encode([
        'success' => false,
        'error'   => 'New password must be at least 6 characters long'
    ]);
    exit;
}

require_once('../../Backend/config.php');  // This file should create a PDO connection in $pdo

try {
    // Verify the old password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($old_password, $user['password'])) {
        echo json_encode([
            'success' => false,
            'error'   => 'Current password is incorrect'
        ]);
        exit;
    }
    
    // Update the password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $result = $stmt->execute([$hashed_password, $_SESSION['user_id']]);
    
    if ($result) {
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
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
