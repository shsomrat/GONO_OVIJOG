<?php
session_start(); // Start the session

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $logged_in = true; // Set a variable to check if user is logged in
} else {
    $logged_in = false; // User is not logged in
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GONO OVIJOG</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon-3.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./assets/css/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
<header>
    <nav class="navbar nav-container">
        <div class="navbar-container">
            <a href="index.php" class="navbar-logo">
                <img src="./assets/img/logo-light.png" alt="Logo" class="logo-img">
            </a>
            <button class="navbar-toggle" id="navbar-toggle">
                <span class="open-icon">☰</span>
                <span class="close-icon">✕</span>
            </button>
            <div class="navbar-links" id="navbar-links">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about_us.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="nav-btn">
                    <?php if ($logged_in): ?>
                        <button class="btn"><a href="account.php">Account<i class="fa-regular fa-circle-user"></i></a></button>
                        <button class="btn"><a href="logout.php">Log out</a></button>
                    <?php else: ?>
                        <button class="btn"><a href="signup.php">Sign Up</a></button>
                        <button class="btn"><a href="login.php">Log In</a></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
