<?php
// Include database configuration
include '../admin/config.php'; // Ensure you have your database connection set up here

include './includes/header.php';

// Handle the form submission for creating a new user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['name']; // Use the correct field name here
    $email = $_POST['email'];
    $plain_password = $_POST['password']; // Store password in plain text
    $phone = $_POST['phone']; // Add phone input to your form
    $role = 'viewer'; // Default role

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $plain_password, $phone, $role);

    // Execute the statement
    if ($stmt->execute()) {
        // User created successfully, now set session variables
        $_SESSION['user_id'] = $stmt->insert_id; // Get the ID of the newly created user
        $_SESSION['full_name'] = $full_name; // Store full name in session

        // Redirect to dashboard
        header("Location: index.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error; // Capture error if user creation fails
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>


<div class="signup-container">
    <h2>Create an Account</h2>
    <!-- Show error message if exists -->
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <form action="" method="post"> <!-- Action set to the same file -->
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" placeholder="Enter your full name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter a password" required>

        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>

        <input type="submit" value="Sign Up">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include './includes/footer.php'; ?>
