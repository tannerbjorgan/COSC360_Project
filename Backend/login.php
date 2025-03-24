<?php
session_start();

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Check if input is email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to the stored URL or dashboard
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            } else {
                header("Location: user-dashboard.php");
            }
            exit;
        } else {
            $_SESSION['login_error'] = "Invalid username/email or password";
            header("Location: login.html");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "An error occurred. Please try again.";
        header("Location: login.html");
        exit;
    }
} else {
    header("Location: login.html");
    exit;

}
?>
