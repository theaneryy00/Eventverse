<?php
$servername = "localhost";
$username = "root";  // Change if using another user
$password = "";      // Set your MySQL password
$database = "event_booking";  // Make sure this matches your DB name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
