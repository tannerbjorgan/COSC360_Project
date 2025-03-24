<?php
session_start();
include 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = trim($_POST['username'] ?? '');
    $pass            = trim($_POST['password'] ?? '');

    try {
        // Find user by email or username
        $sql  = "SELECT * FROM users WHERE email = :login OR username = :login LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':login' => $emailOrUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password if user found
        if ($user && password_verify($pass, $user['password'])) {
            // Store session data
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];

            // Redirect based on admin or user
            if ($user['is_admin'] == 1) {
                header('Location: admin-dashboard.php');
            } else {
                header('Location: user-dashboard.php');
            }
            exit;
        } else {
            header('Refresh:2; url=login.html');
            echo "Invalid credentials. Redirecting back to login page...";
            exit;
        }
    } catch (PDOException $e) {
        echo "DB Error: " . $e->getMessage();
    }
}
?>
