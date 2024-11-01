<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Fetch users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<?php
// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <h1>User Management</h1>
    <button onclick="location.href='add_new_user.php'">Add New User</button>

    <table class="user-table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Address</th>
                <th>Occupation</th>
                <th>Details</th> <!-- Column for user details -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="user-table-body">
            <?php
            // Check if there are any users
            if ($result->num_rows > 0) {
                // Output data for each user
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['email']}</td>
                        <td>
                            <select class='role-select' onchange='changeRole(this, \"{$row['id']}\")'>
                                <option value='admin'" . ($row['role'] === 'admin' ? ' selected' : '') . ">Admin</option>
                                <option value='staff'" . ($row['role'] === 'staff' ? ' selected' : '') . ">Staff</option>
                                <option value='viewer'" . ($row['role'] === 'viewer' ? ' selected' : '') . ">Viewer</option>
                            </select>
                        </td>
                        <td>{$row['address']}</td>
                        <td>{$row['occupation']}</td>
                        <td>{$row['details']}</td> <!-- Display user details -->
                        <td>
                            <button onclick='location.href=`edit_user.php?id={$row['id']}`'>Edit</button>
                            <button onclick='deleteUser({$row['id']})'>Delete</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No users found</td></tr>"; // Updated colspan due to the new details column
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    function changeRole(selectElement, userId) {
        const selectedRole = selectElement.value;
        // Implement logic to update the user's role in the database
        console.log(`User ID: ${userId}, New Role: ${selectedRole}`);
        alert(`Role of user ID ${userId} has been changed to ${selectedRole}.`);
    }

    function deleteUser(userId) {
        // Implement logic to delete the user from the database
        console.log(`User ID: ${userId} has been deleted.`);
        alert(`User ID ${userId} has been deleted.`);
    }
</script>

<?php
// Include the footer
include './includes/footer.php';
?>
