<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Optional: Check user role
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff') {
    // If user is not admin or staff, redirect to a different page or show an error
    header("Location: unauthorized.php"); // Redirect to unauthorized page if needed
    exit();
}
?>
