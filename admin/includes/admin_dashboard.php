<?php
// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<h3>Please log in.</h3>";
    exit();
}

// Fetch all complaint data for admin
$sql = "SELECT c.id, c.title, d.title AS department, c.filled_by, c.status,
               c.victim_name, c.victim_email, c.victim_phone, c.attached_doc,
               c.description, c.created_at
        FROM complaints c
        JOIN departments d ON c.department = d.id";

// Execute the query for complaints
$result = $conn->query($sql);

// Fetch total number of complaints and active complaints
$totalComplaintsQuery = "SELECT COUNT(*) AS total FROM complaints";
$totalResult = $conn->query($totalComplaintsQuery);
$totalComplaints = $totalResult->fetch_assoc()['total'];

$activeComplaintsQuery = "SELECT COUNT(DISTINCT complaint_id) AS active FROM complaint_update WHERE status NOT IN ('Rejected', 'Pending')";
$activeResult = $conn->query($activeComplaintsQuery);
$activeComplaints = $activeResult->fetch_assoc()['active'];

?>

<div class="content">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <?php
        // Display user information
        if (isset($_SESSION['user_id']) && isset($_SESSION['full_name'])) {
            echo "<h5>ID=" . $_SESSION['user_id'] . ", Name=" . $_SESSION['full_name'] . "</h5>";
        } else {
            echo "<h3>Please log in.</h3>";
        }
        ?>
        <button onclick="location.href='add_new_complaint.php?user_id=<?php echo $_SESSION['user_id']?>'">Add New Complaint</button>
    </div>

    <div class="card">
        <h2>Overview</h2>
        <p><strong>Number of Complaints Received:</strong> <span id="total-complaints"><?php echo $totalComplaints; ?></span></p>
        <p><strong>Number of Active Complaints:</strong> <span id="active-complaint"><?php echo $activeComplaints; ?></span></p>
    </div>

    <div class="case-list">
        <h2>Complaints</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Handle By</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            // Fetch the latest update for this complaint ID from the complaint_update table
                            $complaintId = $row['id'];
                            $updateQuery = "
                                SELECT cu.status, u.full_name AS handler_name
                                FROM complaint_update cu
                                LEFT JOIN users u ON cu.handler_id = u.id
                                WHERE cu.complaint_id = $complaintId
                                ORDER BY cu.created_at DESC LIMIT 1";
                            $updateResult = $conn->query($updateQuery);

                            if ($updateResult->num_rows > 0) {
                                $updateRow = $updateResult->fetch_assoc();
                                $status = $updateRow['status'];
                                $handlerName = $updateRow['handler_name'] ?? 'Unassigned';
                            } else {
                                $status = $row['status'];
                                $handlerName = 'Unassigned';
                            }
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($handlerName); ?></td>
                                <td><?php echo htmlspecialchars($status); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No complaints found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="case-updates">
        <h2>Complaint Updates</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Handle By</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch complaint updates with department title and handler's full name
                    $updateSql = "
                        SELECT cu.complaint_id, cu.complaint_title, d.title AS department_title, u.full_name AS handler_name,
                               cu.status, cu.complaint_description, cu.created_at
                        FROM complaint_update cu
                        JOIN departments d ON cu.department = d.id
                        LEFT JOIN users u ON cu.handler_id = u.id
                    ";
                    $updateResult = $conn->query($updateSql);
                    ?>

                    <?php if ($updateResult->num_rows > 0): ?>
                        <?php while ($updateRow = $updateResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $updateRow['complaint_id']; ?></td>
                                <td><?php echo htmlspecialchars($updateRow['complaint_title']); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['department_title']); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['handler_name'] ?? 'Unassigned'); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['status']); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['complaint_description']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($updateRow['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No complaint updates found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
