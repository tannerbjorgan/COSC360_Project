<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Backend/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable error reporting for development (remove or lower in production)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Retrieve manual input values
    $title    = isset($_POST['postTitle']) ? trim($_POST['postTitle']) : '';
    $subtitle = isset($_POST['postSubtitle']) ? trim($_POST['postSubtitle']) : '';
    $content  = isset($_POST['postContent']) ? trim($_POST['postContent']) : '';

    // Only process the DOCX file if the text inputs are still empty.
    if (isset($_FILES['wordFile']) && $_FILES['wordFile']['error'] === UPLOAD_ERR_OK
        && empty($title) && empty($subtitle) && empty($content)) {

        $file = $_FILES['wordFile'];
        
        // Validate file extension: allow only DOCX files.
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'docx') {
            echo "<p style='color:red;'>Error: Only DOCX files are allowed for upload.</p>";
            echo "<a href='../create-post.html'>Go back</a>";
            exit;
        }
        
        // Process the DOCX file using ZipArchive
        $zip = new ZipArchive;
        if ($zip->open($file['tmp_name']) === true) {
            $index = $zip->locateName('word/document.xml');
            if ($index !== false) {
                $data = $zip->getFromIndex($index);
                $zip->close();
                
                // Load XML and extract text content
                $xml = new DOMDocument();
                $xml->loadXML($data);
                $rawText = strip_tags($xml->saveXML());
                
                // Normalize line breaks and split into lines
                $rawText = preg_replace('/\r\n|\r|\n/', "\n", trim($rawText));
                $lines = array_filter(array_map('trim', explode("\n", $rawText)));
                $lines = array_values($lines); // re-index the array
                
                if (count($lines) < 3) {
                    echo "<p style='color:red;'>Error: The DOCX file must have at least three lines (title, subtitle, and content).</p>";
                    echo "<a href='../create-post.html'>Go back</a>";
                    exit;
                }
                
                // Use the first line as title, second as subtitle, rest as content.
                $title    = $lines[0];
                $subtitle = $lines[1];
                $content  = implode("\n", array_slice($lines, 2));
            } else {
                $zip->close();
                echo "<p style='color:red;'>Error: Could not find document.xml inside the DOCX file.</p>";
                echo "<a href='../create-post.html'>Go back</a>";
                exit;
            }
        } else {
            echo "<p style='color:red;'>Error: Could not open DOCX file.</p>";
            echo "<a href='../create-post.html'>Go back</a>";
            exit;
        }
    } else {
        // If no file is uploaded, then ensure that manual input for title and content is provided.
        if (empty($title) || empty($content)) {
            echo "<p style='color:red;'>Error: Title and content are required.</p>";
            echo "<a href='../create-post.html'>Go back</a>";
            exit;
        }
    }
    
    // Insert post into the database using PDO.
    // Note: This assumes your posts table has a 'subtitle' column.
    try {
        require_once('../../Backend/config.php');  // This file should set up the PDO connection in $pdo

        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, subtitle, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $subtitle, $content]);
        
        // Redirect to the user dashboard upon successful insertion.
        echo "<script>window.location.href = '../../Backend/user-dashboard.php';</script>";
        exit;
    } catch (Exception $e) {
        error_log("Error in create_post.php: " . $e->getMessage());
        echo "<p style='color:red;'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<a href='../create-post.html'>Go back</a>";
        exit;
    }
} else {
    header("Location: ../create-post.html");
    exit;
}
?>
