<?php
// Check if a user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to dashboard if session exists
    exit();
}

require_once '../admin/config.php'; // Include your database connection file
include './includes/header.php';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL query to check login credentials
    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE full_name = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password); // Binding parameters (username and password)

    if ($stmt->execute()) {
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Credentials match, so log the user in
            $stmt->bind_result($user_id, $full_name, $plain_password, $role);
            $stmt->fetch();

            // Store user data in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role; // Store user role in session

            // Redirect to dashboard
            header("Location: index.php");
            exit();
        } else {
            // Invalid credentials
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Error executing query: " . $conn->error;
    }

    $stmt->close();
}
?>

    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="./assets/img/logo-dark.png" alt="Organization Logo">
            </div>
            <h2>Admin Login</h2>
            <!-- Display error message if exists -->
            <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <form action="" method="post">
                <div class="input-box">
                    <input type="text" name="username" id="username" required>
                    <label for="username">Username</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" required>
                    <label for="password">Password</label>
                </div>
                <button type="submit" class="login-btn">Log In</button>
                <a href="signup.php" class="signin">Sign Up</a>
            </form>
        </div>
    </div>
    <?php include './includes/footer.php'; ?>
