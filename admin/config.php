<?php
// Database configuration
$servername = "localhost"; // Change if your database is hosted somewhere else
$username = "root";
$password = "";
$dbname = "gono_ovijog";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful
// echo "Connected successfully"; // Uncomment this line to test the connection
?>
