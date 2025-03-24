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

    try {
        // Insert user (is_admin=0, then normal user)
        $sql = "INSERT INTO users (name, email, username, password, is_admin)
                VALUES (:name, :email, :username, :password, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':username' => $username,
            ':password' => $hashedPassword
        ]);

        $_SESSION['user_id']  = $pdo->lastInsertId();
        $_SESSION['is_admin'] = 0;

        // Redirect to user dashboard
        header('Location: user-dashboard.php');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
