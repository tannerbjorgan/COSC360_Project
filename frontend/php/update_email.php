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
$email = isset($data['email']) ? trim($data['email']) : '';

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid email address'
    ]);
    exit;
}

require_once('../../Backend/config.php'); // This should create a PDO connection in $pdo

try {
    // Check if the email is already in use by another user.
    $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'error'   => 'Email address is already in use'
        ]);
        exit;
    }
    
    // Update the user's email.
    $sql = "UPDATE users SET email = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$email, $_SESSION['user_id']]);
    
    if ($result) {
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
        'error'   => 'Database error: ' . $e->getMessage()
    ]);
}
