<?php
$servername = "cosc360.ok.ubc.ca";
$username = "dfeng06";      // XAMPP default username
$password = "dfeng06";          // XAMPP default password is empty
$dbname = "dfeng06"; // Make sure this database exists in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>

