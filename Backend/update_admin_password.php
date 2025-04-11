<?php
session_start();
require_once 'config.php';

// Ensure user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['oldPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        header('Location: admin-profile.php?error=empty_fields');
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        header('Location: admin-profile.php?error=passwords_dont_match');
        exit;
    }

    try {
        // Verify old password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? AND is_admin = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            header('Location: admin-profile.php?error=wrong_password');
            exit;
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ? AND is_admin = 1");
        $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

        if ($stmt->rowCount() > 0) {
            header('Location: admin-profile.php?success=password_updated');
        } else {
            header('Location: admin-profile.php?error=update_failed');
        }
    } catch (PDOException $e) {
        error_log("Error updating admin password: " . $e->getMessage());
        header('Location: admin-profile.php?error=server_error');
    }
} else {
    header('Location: admin-profile.php');
}
exit; 