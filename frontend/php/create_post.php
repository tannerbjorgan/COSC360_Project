<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Backend/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $title    = isset($_POST['postTitle']) ? trim($_POST['postTitle']) : '';
    $content  = isset($_POST['postContent']) ? trim($_POST['postContent']) : '';

    // Debug output
    error_log("Received post data - Title: " . $title);
    error_log("User ID from session: " . $user_id);

    if (empty($title) || empty($content)) {
        echo "<p style='color:red;'>Error: Title and content are required.</p>";
        echo "<a href='../create-post.html'>Go back</a>";
        exit();
    }

    try {
        $servername = "localhost";
        $username = "root";     
        $password = "";         
        $dbname = "dfeng06"; 

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Debug output
        error_log("Database connection successful");

        // Updated SQL to match actual table structure
        $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("iss", $user_id, $title, $content);
        
        // Debug output before execution
        error_log("About to execute SQL statement");
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Debug output after successful insertion
        error_log("Post successfully inserted with ID: " . $conn->insert_id);

        $stmt->close();
        $conn->close();

        // Use JavaScript for redirect to ensure headers haven't been sent
        echo "<script>window.location.href = '../../Backend/user-dashboard.php';</script>";
        exit();

    } catch (Exception $e) {
        error_log("Error in create_post.php: " . $e->getMessage());
        echo "<p style='color:red;'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<a href='../create-post.html'>Go back</a>";
    }
} else {
    header("Location: ../create-post.html");
    exit();
}
?>
