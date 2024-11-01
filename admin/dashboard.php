<?php

// Include data_table.php to create tables if they do not exist
include './data_table.php';

// Include database configuration
include './config.php';

// Include the header
include './includes/header.php';

// Include the sidebar (if needed)
include './includes/sidebar.php';


// Check if user is logged in and retrieve role
if (!isset($_SESSION['role'])) {
    echo "<h3>Please log in to view this page.</h3>";
    include './includes/footer.php';
    exit();
}

// Determine user role
$userRole = $_SESSION['role'];

// Prepare SQL query based on user role
if ($userRole == 'admin') {
    // Include admin dashboard
    include './includes/admin_dashboard.php';
} elseif ($userRole == 'staff') {
    // Include staff dashboard
    include './includes/staff_dashboard.php';
} else {
    // Prevent non-admin and non-staff from seeing anything
    echo "<h3>You do not have permission to view this data.</h3>";
    include './includes/footer.php';
    exit();
}
?>

<!-- Include the footer -->
<?php include './includes/footer.php'; ?>
