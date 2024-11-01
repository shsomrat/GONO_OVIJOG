<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Include the sidebar (if needed)
include './includes/sidebar.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<h3>Please log in.</h3>";
    exit();
}

// Define the SQL query based on user role
if ($_SESSION['role'] === 'admin') {
    // Admin can see all complaint updates
    $sql = "
        SELECT cu.*, c.title AS complaint_title, c.created_at AS complaint_created_at, c.victim_name, c.victim_email, c.victim_phone, c.description AS complaint_description, d.title AS department_title
        FROM complaint_update cu
        JOIN complaints c ON cu.complaint_id = c.id
        JOIN departments d ON c.department = d.id
        WHERE (cu.complaint_id, cu.created_at) IN (
            SELECT complaint_id, MAX(created_at)
            FROM complaint_update
            GROUP BY complaint_id
        )
    ";
} else if ($_SESSION['role'] === 'staff') {
    // Staff can see their own complaint updates
    $handler_id = $_SESSION['user_id'];
    $sql = "
        SELECT cu.*, c.title AS complaint_title, c.created_at AS complaint_created_at, c.victim_name, c.victim_email, c.victim_phone, c.description AS complaint_description, d.title AS department_title
        FROM complaint_update cu
        JOIN complaints c ON cu.complaint_id = c.id
        JOIN departments d ON c.department = d.id
        WHERE cu.handler_id = '$handler_id'
        AND (cu.complaint_id, cu.created_at) IN (
            SELECT complaint_id, MAX(created_at)
            FROM complaint_update
            WHERE handler_id = '$handler_id'
            GROUP BY complaint_id
        )
    ";
}

// Execute the query
$result = $conn->query($sql);
?>

<div class="content">
    <div class="complaint-list">
        <h2>Complaint Updates</h2>
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search by Title, Email, Phone, Name or Complaint ID...">
            <button onclick="filterComplaints()">Search</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Title</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Victim Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                   
                    <th>Last Update At</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="complaint-table-body">
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row['complaint_id']) . '</td>
                                <td>' . htmlspecialchars($row['complaint_title']) . '</td>
                                <td>' . htmlspecialchars($row['department_title']) . '</td>
                                <td>' . htmlspecialchars($row['status']) . '</td>
                                <td>' . htmlspecialchars($row['victim_name']) . '</td>
                                <td>' . htmlspecialchars($row['victim_email']) . '</td>
                                <td>' . htmlspecialchars($row['victim_phone']) . '</td>

                                <td>' . date('F j, Y', strtotime($row['created_at'])) . '</td>
                                <td>' . date('F j, Y', strtotime($row['complaint_created_at'])) . '</td>
                                <td class="actions">
                                    <a class="btn" href="complaint_details.php?complaint_id=' . htmlspecialchars($row['complaint_id']) . '">View</a>
                                    <a class="btn" href="complaint_update.php?complaint_id=' . htmlspecialchars($row['complaint_id']) . '">Edit</a>
                                    <a class="btn" href="complaint_delete.php?complaint_id=' . htmlspecialchars($row['complaint_id']) . '" onclick="return confirm(\'Are you sure you want to delete this complaint?\')">Delete</a>
                                </td>
                            </tr>';
                    }
                } else {
                    // If no complaints are found, display a single empty row with a message
                    echo '<tr><td colspan="11" style="text-align: center;">No complaints found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function filterComplaints() {
        const input = document.getElementById('search').value.toLowerCase();
        const tableBody = document.getElementById('complaint-table-body');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const complaintId = rows[i].cells[0].textContent.toLowerCase();
            const title = rows[i].cells[1].textContent.toLowerCase();
            const victimName = rows[i].cells[4].textContent.toLowerCase();
            const victimEmail = rows[i].cells[5].textContent.toLowerCase();
            const victimPhone = rows[i].cells[6].textContent.toLowerCase();

            // Check if the input matches any of the searched columns
            const matches = complaintId.indexOf(input) > -1 ||
                            title.indexOf(input) > -1 ||
                            victimName.indexOf(input) > -1 ||
                            victimEmail.indexOf(input) > -1 ||
                            victimPhone.indexOf(input) > -1;

            rows[i].style.display = matches ? '' : 'none';
        }
    }
</script>

<?php
// Include the footer
include './includes/footer.php';
?>
