<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Check if the user ID is provided
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch the user's data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<p>User not found.</p>";
        exit;
    }
} else {
    echo "<p>No user ID provided.</p>";
    exit;
}

// Handle the form submission for updating the user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);
    $details = trim($_POST['details']);
    $address = trim($_POST['address']);
    $occupation = trim($_POST['occupation']);

    // Update the user's data in the database
    $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, details = ?, address = ?, occupation = ? WHERE id = ?");
    $updateStmt->bind_param("sssssssi", $fullName, $email, $phone, $role, $details, $address, $occupation, $userId);

    if ($updateStmt->execute()) {
        echo "<p>User updated successfully!</p>";
    } else {
        echo "<p>Error updating user: " . $updateStmt->error . "</p>";
    }

    $updateStmt->close();
}

// Close the database connection
$conn->close();
?>

<?php
// Include the sidebar
include './includes/sidebar.php';
?>

<div class="content">
    <h1>Edit User</h1>
    <form id="edit-user-form" method="POST" action="">
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
        <input type="text" name="occupation" value="<?php echo htmlspecialchars($user['occupation']); ?>" required>
        <select name="role" required>
            <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="staff" <?php if ($user['role'] === 'staff') echo 'selected'; ?>>Staff</option>
            <option value="viewer" <?php if ($user['role'] === 'viewer') echo 'selected'; ?>>Viewer</option>
        </select>
        <textarea name="details" required><?php echo htmlspecialchars($user['details']); ?></textarea>
        <button type="submit">Update User</button>
    </form>
</div>

<?php
// Include the footer
include './includes/footer.php';
?>
