<?php
session_start();
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form fields
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? ''); 
    $pass     = trim($_POST['password'] ?? '');

    // Hash the password
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

    // Handle profile image upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file = $_FILES['profile_image'];
        
        if (!in_array($file['type'], $allowed_types)) {
            die("Error: Invalid file type. Only JPG, PNG and GIF are allowed.");
        }

        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            die("Error: File too large. Maximum size is 5MB.");
        }

        // Use absolute path for the uploads folder.
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/COSC360_PROJECT/frontend/images/profile_images/";
        if (!file_exists($upload_dir)) {
            // Attempt to create the directory if it doesn't exist.
            if (!mkdir($upload_dir, 0777, true)) {
                die("Error: Failed to create directory: " . $upload_dir);
            }
        }
        
        // Debug: Confirm temporary file exists.
        if (!is_uploaded_file($file['tmp_name'])) {
            die("Error: Temporary file not found.");
        }

        // Generate unique filename.
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Attempt to move the uploaded file.
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save a relative path for later use (e.g., storing in the database).
            $profile_image = "images/profile_images/" . $filename;
        } else {
            die("Error: Failed to save image.");
        }
    }

    try {
        // Insert new user data.
        $sql = "INSERT INTO users (name, email, username, password, profile_image, is_admin)
                VALUES (:name, :email, :username, :password, :profile_image, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'          => $name,
            ':email'         => $email,
            ':username'      => $username,
            ':password'      => $hashedPassword,
            ':profile_image' => $profile_image
        ]);

        $_SESSION['user_id']  = $pdo->lastInsertId();
        $_SESSION['is_admin'] = 0;

        // Redirect to the user dashboard.
        header('Location: user-dashboard.php');
        exit;
    } catch (PDOException $e) {
        // If the database insertion fails, remove the uploaded file if it exists.
        if ($profile_image && file_exists($filepath)) {
            unlink($filepath);
        }
        echo "Error: " . $e->getMessage();
    }
}
?>
