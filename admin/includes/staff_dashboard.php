<?php
// Start session and check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    echo "<h3>Please log in as a staff member.</h3>";
    exit();
}

// Database connection (ensure $conn is defined here)
// Assuming $conn is already connected, otherwise include your connection file here.

// Fetch the full name from the 'users' table
$user_id = $_SESSION['user_id'];
$userQuery = "SELECT full_name FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
if ($userResult->num_rows > 0) {
    $_SESSION['full_name'] = $userResult->fetch_assoc()['full_name'];
}

// SQL query to fetch complaint updates using created_at
$sql = "
    SELECT cu.complaint_id, cu.status, cu.created_at AS updated_at, c.victim_name, c.victim_email, c.victim_phone, c.title, d.title AS department, cu.created_at AS complaint_created_at
    FROM complaint_update cu
    JOIN (
        SELECT complaint_id, MAX(created_at) AS latest_update
        FROM complaint_update
        WHERE handler_id = ?
        GROUP BY complaint_id
    ) latest_updates ON cu.complaint_id = latest_updates.complaint_id AND cu.created_at = latest_updates.latest_update
    JOIN complaints c ON cu.complaint_id = c.id
    JOIN departments d ON c.department = d.id
    WHERE cu.handler_id = ?
    ORDER BY cu.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Total complaints assigned, counting distinct complaint_id
$totalComplaintsQuery = "SELECT COUNT(DISTINCT complaint_id) AS total FROM complaint_update WHERE handler_id = ?";
$stmt = $conn->prepare($totalComplaintsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalComplaints = $totalResult->fetch_assoc()['total'];

// Active complaints (excluding Resolved and Rejected statuses)
$activeComplaintsQuery = "
    SELECT COUNT(DISTINCT cu.complaint_id) AS active
    FROM complaint_update cu
    JOIN complaints c ON cu.complaint_id = c.id
    WHERE cu.handler_id = ? AND c.status NOT IN ('Resolved', 'Rejected')";
$stmt = $conn->prepare($activeComplaintsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$activeResult = $stmt->get_result();
$activeComplaints = $activeResult->fetch_assoc()['active'];
?>
<div class="content">
    <div class="dashboard-header">
        <h1><?php echo htmlspecialchars($_SESSION['full_name']); ?>'s Dashboard</h1>
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['full_name'])): ?>
            <h5>ID=<?php echo $_SESSION['user_id']; ?>, Name=<?php echo htmlspecialchars($_SESSION['full_name']); ?></h5>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Overview</h2>
        <p><strong>Number of Complaints Assigned:</strong> <span id="total-complaints"><?php echo $totalComplaints; ?></span></p>
        <p><strong>Number of Active Cases:</strong> <span id="active-cases"><?php echo $activeComplaints; ?></span></p>
    </div>

    <div class="case-list">
        <h2>Assigned Cases</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Victim Name</th>
                        <th>Victim Email</th>
                        <th>Victim Phone</th>
                        <th>Last Updated</th>
                        <th>Time</th> <!-- New column for Time -->
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['complaint_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['victim_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['victim_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['victim_phone']); ?></td>
                                <td><?php echo $row['updated_at']; ?></td>
                                <td><?php echo $row['complaint_created_at']; ?></td> <!-- Display complaint_created_at -->
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No assigned cases found.</td>
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
                        <th>Status</th>
                        <th>Description</th>
                        <th>Updated At</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch complaint updates for the staff member
                    $updatesQuery = "
                        SELECT cu.complaint_id, c.title, d.title AS department, cu.status, cu.description, cu.created_at AS updated_at, c.created_at AS complaint_created_at
                        FROM complaint_update cu
                        JOIN complaints c ON cu.complaint_id = c.id
                        JOIN departments d ON c.department = d.id
                        WHERE cu.handler_id = ?
                        ORDER BY cu.created_at DESC";
                    $stmt = $conn->prepare($updatesQuery);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $updatesResult = $stmt->get_result();

                    if ($updatesResult->num_rows > 0):
                        while ($updateRow = $updatesResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $updateRow['complaint_id']; ?></td>
                                <td><?php echo htmlspecialchars($updateRow['title']); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['department']); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['status']); ?></td>
                                <td><?php echo htmlspecialchars($updateRow['description']); ?></td>
                                <td><?php echo $updateRow['updated_at']; ?></td>
                                <td><?php echo date('F j, Y', strtotime($updateRow['updated_at'])); ?></td>
                                <td><?php echo date('F j, Y', strtotime($updateRow['complaint_created_at'])); ?></td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr>
                            <td colspan="7">No updates found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
