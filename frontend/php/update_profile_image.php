<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

if (!isset($_FILES['profile_image'])) {
    echo json_encode(['error' => 'No image file uploaded']);
    exit;
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['profile_image'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG and GIF are allowed']);
    exit;
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    echo json_encode(['error' => 'File too large. Maximum size is 5MB']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../images/profile_images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate a unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = $user_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Move the uploaded file to the target directory
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['error' => 'Failed to save image']);
    exit;
}

// Use PDO for database operations
require_once('../../Backend/config.php'); // This file should set up $pdo as a PDO connection

try {
    // Retrieve the old profile image (if any) for deletion
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete the old profile image if it exists on disk
    if ($user && $user['profile_image'] && file_exists('../' . $user['profile_image'])) {
        unlink('../' . $user['profile_image']);
    }

    // Update the user's profile image in the database.
    // Note: We store a relative path (relative to where the images are served).
    $image_path = 'images/profile_images/' . $filename;
    $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
    $result = $stmt->execute([$image_path, $user_id]);

    if ($result) {
        echo json_encode([
            'success'   => true,
            'image_url' => $image_path
        ]);
    } else {
        // If the update fails, remove the uploaded file
        unlink($filepath);
        echo json_encode(['error' => 'Failed to update database']);
    }
} catch (Exception $e) {
    // Remove the uploaded file if an exception occurs
    unlink($filepath);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
