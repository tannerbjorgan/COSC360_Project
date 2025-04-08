<?php
session_start(); 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? ''); 
    $password = trim($_POST['password'] ?? ''); 

    if (empty($username) || empty($password)) {
        header("Location: login.html?error=empty");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['is_admin']  = $user['is_admin'];

            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit;
            }
            if ($_SESSION['is_admin'] == 1) {
                header('Location: admin-dashboard.php');
                exit;
            } else {
                header('Location: user-dashboard.php');
                exit;
            }

        } else {
            if (!$user) {
                header("Location: login.html?error=nouser");
            } else {
                header("Location: login.html?error=wrongpass");
            }
            exit; 
        }
    } catch (PDOException $e) {
        error_log("Login PDOException: " . $e->getMessage()); 
        header("Location: login.html?error=server");
        exit;
    }
} else {
    header("Location: login.html?error=nopost");
    exit;
}
?>