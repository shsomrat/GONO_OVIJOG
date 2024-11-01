<?php
session_start(); // Start the session

// Check if a user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if session exists
    exit();
}

require_once 'config.php'; // Include your database connection file

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
            $stmt->bind_result($user_id, $full_name, $hashed_password, $role);
            $stmt->fetch();

            // Store user data in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['role'] = $role; // Store user role in session

            // Redirect to dashboard
            header("Location: dashboard.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GONO OVIJOG - Admin Login</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon-3.png">
    <link rel="stylesheet" href="style.css">
</head>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-box {
    background-color: #fff;
    padding: 40px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    text-align: center;
    width: 350px;
}

.logo img {
    width: 100%;
    height: auto;
    margin-bottom: 20px;
}

h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

.input-box {
    position: relative;
    margin-bottom: 30px;
}

.input-box input {
    width: 100%;
    padding: 10px;
    background: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}

.input-box input:focus {
    border-color: #007bff;
}

.input-box label {
    position: absolute;
    left: 10px;
    top: 10px;
    padding: 0 5px;
    background: #fff;
    color: #999;
    transition: 0.3s;
    pointer-events: none;
}

.input-box input:focus + label,
.input-box input:not(:placeholder-shown) + label {
    top: -12px;
    left: 8px;
    font-size: 12px;
    color: #007bff;
}

.login-btn {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

.login-btn:hover {
    background-color: #0056b3;
}
.signin{
    text-align: right;
}

</style>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="./assets/img/logo-dark.png" alt="Organization Logo">
            </div>
            <h2>Admin Login</h2>
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
                <button type="submit" class="login-btn">Login</button>
                <a href="signin.php" class="signin">sign in</a>
            </form>
        </div>
    </div>
</body>
</html>
