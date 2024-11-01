<?php
// Include database configuration
include '../admin/config.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    // Update the user information in the database
    $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {
        // Redirect back to the profile page with a success message
        header("Location: success_profile_update.php?status=success");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: success_profile_update.php?status=error");
        exit();
    }
}
?>
