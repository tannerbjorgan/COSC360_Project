<?php
session_start();
require_once 'config.php';

// Ensure user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = trim($_POST['newEmail'] ?? '');
    
    if (empty($newEmail)) {
        header('Location: admin-profile.php?error=empty_email');
        exit;
    }

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        header('Location: admin-profile.php?error=invalid_email');
        exit;
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$newEmail, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            header('Location: admin-profile.php?error=email_exists');
            exit;
        }

        // Update email
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ? AND is_admin = 1");
        $stmt->execute([$newEmail, $_SESSION['user_id']]);

        if ($stmt->rowCount() > 0) {
            header('Location: admin-profile.php?success=email_updated');
        } else {
            header('Location: admin-profile.php?error=update_failed');
        }
    } catch (PDOException $e) {
        error_log("Error updating admin email: " . $e->getMessage());
        header('Location: admin-profile.php?error=server_error');
    }
} else {
    header('Location: admin-profile.php');
}
exit; 