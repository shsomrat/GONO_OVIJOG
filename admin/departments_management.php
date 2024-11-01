<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Fetch departments from the database
$sql = "SELECT * FROM departments";
$result = $conn->query($sql);
?>

<?php
// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <h1>Department Management</h1>
    <button onclick="location.href='add_new_department.php'">Add New Department</button>

    <table class="department-table">
        <thead>
            <tr>
                <th>Department ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="department-table-body">
            <?php
            // Check if there are departments and display them
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['description']}</td>
                        <td>
                            <button onclick='location.href=`edit_department.php?id={$row['id']}`'>Edit</button>
                            <button onclick='deleteDepartment({$row['id']})'>Delete</button>
                        </td>

                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No departments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    function deleteDepartment(departmentId) {
        if (confirm(`Are you sure you want to delete department ID ${departmentId}?`)) {
            fetch('delete_department.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: departmentId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Department ID ${departmentId} has been deleted.`);
                    location.reload(); // Reload the page to see the updated list
                } else {
                    alert(`Failed to delete department: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error deleting department:', error);
                alert('An error occurred while deleting the department.');
            });
        }
    }
</script>

<?php
// Include the footer
include './includes/footer.php';
?>
