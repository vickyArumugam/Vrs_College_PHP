<?php
include 'cors.php';

// Database credentials
$host = 'localhost';
$db_name = 'paper'; 
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}
?>

