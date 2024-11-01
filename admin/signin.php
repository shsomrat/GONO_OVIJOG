<?php
// Start the session
session_start();

// Include database configuration
include './config.php'; // Ensure you have your database connection set up here

// Check if a user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if session exists
    exit();
}

// Handle the form submission for creating a new user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $plain_password = $_POST['password']; // Store password in plain text
    $phone = $_POST['phone'];
    $role = 'viewer'; // Default role

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $plain_password, $phone, $role);

    // Execute the statement
    if ($stmt->execute()) {
        // User created successfully, now set session variables
        $_SESSION['user_id'] = $stmt->insert_id; // Get the ID of the newly created user
        $_SESSION['full_name'] = $full_name; // Store full name in session
        $_SESSION['role'] = $role; // Store user role in session

        // Redirect to dashboard
        header("Location: dashboard.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GONO OVIJOG - Sign Up</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon-3.png">
    <link rel="stylesheet" href="style.css">
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

        .error {
            color: red;
            margin: 10px 0;
        }

        .success {
            color: green;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="./assets/img/logo-dark.png" alt="Organization Logo">
            </div>
            <h2>Sign Up</h2>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
            <form action="" method="post">
                <div class="input-box">
                    <input type="text" name="full_name" id="full-name" required>
                    <label for="full-name">Full Name</label>
                </div>
                <div class="input-box">
                    <input type="email" name="email" id="email" required>
                    <label for="email">Email Address</label>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="input-box">
                    <input type="text" name="phone" id="phone" required>
                    <label for="phone">Phone Number</label>
                </div>
                <button type="submit" class="login-btn">Sign Up</button>
                <a href="login.php" class="login">Log in</a>
            </form>
        </div>
    </div>
</body>
</html>
