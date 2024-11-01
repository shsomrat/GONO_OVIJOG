<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['full_name']) || $_SESSION['role'] !== 'admin') {
    echo "<h3>Please log in as an admin.</h3>";
    exit(); // Exit if not logged in or not an admin
}

// Fetch complaints with a pending status that are not in the complaint_update table
$sql = "SELECT complaints.*, departments.title AS department_title
        FROM complaints
        JOIN departments ON complaints.department = departments.id
        LEFT JOIN complaint_update ON complaints.id = complaint_update.complaint_id
        WHERE complaints.status = 'pending' AND complaint_update.complaint_id IS NULL"; // Exclude rows where complaint_id exists in complaint_update

$result = $conn->query($sql);

// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <div class="complaint-list">
        <h2>Pending Complaints</h2>
        <h5>ID=<?php echo $_SESSION['user_id']; ?>, Name=<?php echo $_SESSION['full_name']; ?></h5>
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search by Title, Email, Phone, or Name...">
            <button onclick="filterComplaints()">Search</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Department</th>
                    <th>Description</th>
                    <th>Attach File</th>
                    <th>Date Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="complaint-table-body">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['victim_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['victim_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['victim_phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['department_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($row['attached_doc']); ?>" target="_blank">View</a>
                        </td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <form method="GET" action="approve_reject.php">
                                <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                                <button type="submit">View</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
            const fullName = rows[i].cells[1].textContent.toLowerCase();
            const email = rows[i].cells[2].textContent.toLowerCase();
            const phone = rows[i].cells[3].textContent.toLowerCase();
            const department = rows[i].cells[4].textContent.toLowerCase();
            const description = rows[i].cells[5].textContent.toLowerCase();

            // Check if any of the fields match the input
            const matches = complaintId.includes(input) || fullName.includes(input) || email.includes(input) || phone.includes(input) || department.includes(input) || description.includes(input);
            rows[i].style.display = matches ? '' : 'none';
        }
    }
</script>

<?php
// Include the footer
include './includes/footer.php';
?>
