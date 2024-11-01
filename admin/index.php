<?php
// Include data_table.php to create tables if they do not exist
include './data_table.php';

// Include the header
include './includes/header.php';

// Check if a user is already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php"); // Redirect to dashboard if session exists
  exit();
}
else {
  header("Location:login.php"); // Redirect to dashboard if session exists
  exit();
}

?>