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

        // Set the upload directory (assumes the folder already exists)
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/file_uploads/";
        
        // Generate a unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Attempt to move the uploaded file to the target directory.
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save a relative path for later use (e.g., for displaying the image)
            $profile_image = "file_uploads/" . $filename;
        } else {
            die("Error: Failed to save image.");
        }
    }

    try {
        // Insert user (is_admin=0 for a normal user)
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

        // Redirect to user dashboard
        header('Location: user-dashboard.php');
        exit;
    } catch (PDOException $e) {
        // If database insertion fails, delete the uploaded image if it exists.
        if ($profile_image && file_exists($filepath)) {
            unlink($filepath);
        }

        echo "Error: " . $e->getMessage();
    }
}
?>
