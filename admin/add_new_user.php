<?php
// Include data_table.php to create tables if they do not exist
include './data_table.php';

// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';


// Now you can use $conn to interact with the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Your existing code for processing the form goes here
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);
    $details = trim($_POST['details']); // Getting the details field
    $address = trim($_POST['address']); // New address field
    $occupation = trim($_POST['occupation']); // New occupation field

    // Validate that all fields are filled
    if (empty($fullName) || empty($email) || empty($password) || empty($phone) || empty($role) || empty($details) || empty($address) || empty($occupation)) {
        echo "<p>Please fill in all fields.</p>";
    } else {
        // Check if the email already exists
        $emailCheckQuery = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $emailCheckQuery->bind_param("s", $email);
        $emailCheckQuery->execute();
        $result = $emailCheckQuery->get_result();

        if ($result->num_rows > 0) {
            echo "<p>Email already exists. Please use a different email.</p>";
        } else {
            // Insert the user data into the database
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, role, details, address, occupation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fullName, $email, $password, $phone, $role, $details, $address, $occupation);

            if ($stmt->execute()) {
                $_SESSION['message'] = "User added successfully!";
            } else {
                $_SESSION['message'] = "Error adding user: " . $stmt->error;
            }

            $stmt->close();
        }

        $emailCheckQuery->close();
    }
}

// Close the database connection (optional here since the script will end)
$conn->close();
?>

<?php
// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <div>    <?php if (isset($_SESSION['message'])): ?>
                <p><?php echo $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']); // Clear message after displaying it ?>
            <?php endif; ?>
    </div>
    <h1>Add New User</h1>
    <form id="add-user-form" method="POST" action="">
        <input type="text" name="full_name" id="full-name" placeholder="Full Name" required>
        <input type="email" name="email" id="email" placeholder="Email Address" required>
        <input type="text" name="password" id="password" placeholder="Password" required>
        <input type="text" name="phone" id="phone" placeholder="Phone Number" required>
        <input type="text" name="address" id="address" placeholder="Address" required> <!-- New address input -->
        <input type="text" name="occupation" id="occupation" placeholder="Occupation" required> <!-- New occupation input -->
        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
            <option value="viewer">Viewer</option>
        </select>
        <textarea name="details" id="details" placeholder="Enter user details" required></textarea>
        <button class="add_new" type="submit">Add User</button>
    </form>
</div>

<?php
// Include the footer
include './includes/footer.php';
?>
