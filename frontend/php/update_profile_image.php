<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not authenticated']));
}

if (!isset($_FILES['profile_image'])) {
    die(json_encode(['error' => 'No image file uploaded']));
}

$user_id = $_SESSION['user_id'];
$file = $_FILES['profile_image'];

// Validate file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    die(json_encode(['error' => 'Invalid file type. Only JPG, PNG and GIF are allowed']));
}

$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    die(json_encode(['error' => 'File too large. Maximum size is 5MB']));
}

// Create uploads directory if it doesn't exist
$upload_dir = '../images/profile_images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = $user_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    die(json_encode(['error' => 'Failed to save image']));
}

// Update database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dfeng06";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    unlink($filepath); // Delete uploaded file if database connection fails
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get old profile image path
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Delete old profile image if it exists
if ($user['profile_image'] && file_exists('../' . $user['profile_image'])) {
    unlink('../' . $user['profile_image']);
}

// Update user's profile image in database
$image_path = 'images/profile_images/' . $filename;
$stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
$stmt->bind_param("si", $image_path, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'image_url' => $image_path
    ]);
} else {
    unlink($filepath); // Delete uploaded file if database update fails
    echo json_encode(['error' => 'Failed to update database']);
}

$conn->close();
?> 