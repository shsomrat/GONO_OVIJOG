<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    session_unset();
    // Destroy the session
    session_destroy();

    // Optionally, you can set a logout message (not shown in this code)
    // header("Location: login.php?message=You have successfully logged out.");
} else {
    // If the user is not logged in, redirect to login page without a message
    header("Location: login.php");
    exit();
}

// Redirect to the login page
header("Location: login.php");
exit();
?>
