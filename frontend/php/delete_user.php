<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User ID is required']);
    exit;
}

$userIdToDelete = (int)$data['user_id'];
$currentUserId = (int)$_SESSION['user_id'];

if ($userIdToDelete === $currentUserId) {
    echo json_encode(['success' => false, 'error' => "You can't delete your own account"]);
    exit;
}

require_once('../../Backend/config.php'); // Sets up $pdo

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userIdToDelete]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No user deleted']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
}
?>
