<?php
session_start(); // Start the session

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $logged_in = true; // Set a variable to check if user is logged in
} else {
    $logged_in = false; // User is not logged in
}
?>
