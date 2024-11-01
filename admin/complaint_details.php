<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Check if the complaint_id is set in the URL
if (!isset($_GET['complaint_id'])) {
    die("Complaint ID not provided.");
}

$complaint_id = (int)$_GET['complaint_id'];

// Fetch complaint details from the complaints table
$sqlComplaint = "SELECT
                    c.id,
                    c.title,
                    d.title AS department_title,
                    c.filled_by,
                    c.status,
                    c.victim_name,
                    c.victim_email,
                    c.victim_phone,
                    c.description,
                    c.created_at
                FROM
                    complaints c
                JOIN
                    departments d ON c.department = d.id
                WHERE
                    c.id = ?";

$stmt = $conn->prepare($sqlComplaint);
$stmt->bind_param('i', $complaint_id);
$stmt->execute();
$complaintResult = $stmt->get_result();

if ($complaintResult->num_rows === 0) {
    die("Complaint not found.");
}

$complaint = $complaintResult->fetch_assoc();


// Fetch updates from the complaint_update table with handler name and update status
$sqlUpdates = "SELECT
                    cu.id,
                    cu.handler_id,
                    cu.status AS update_status,
                    cu.heading,
                    cu.description,
                    cu.created_at,
                    cu.attached_doc,  /* Fetching attached document from updates */
                    u.full_name AS handler_name
                FROM
                    complaint_update cu
                LEFT JOIN
                    users u ON cu.handler_id = u.id
                WHERE
                    cu.complaint_id = ?
                ORDER BY
                    cu.created_at ASC"; // Order by ascending for oldest to newest

$stmtUpdates = $conn->prepare($sqlUpdates);
$stmtUpdates->bind_param('i', $complaint_id);
$stmtUpdates->execute();
$updatesResult = $stmtUpdates->get_result();

// Check if there's a status in the updates table for the current complaint
$currentStatus = $complaint['status'];
if ($updatesResult->num_rows > 0) {
    $latestUpdate = $updatesResult->fetch_assoc();
    $currentStatus = $latestUpdate['update_status'];
    // Reset the result pointer for displaying all updates
    $updatesResult->data_seek(0);
}

// Calculate days since submission
$submissionDate = new DateTime($complaint['created_at']);
$currentDate = new DateTime();
$daysPassed = $currentDate->diff($submissionDate)->days;
?>

<style>
    .complaint-wrapper {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    .complaint-description,
    .complaint-evidence,
    .complaint-updates,
    .complaint-resolution {
        margin-top: 20px;
        padding: 15px;
        border-radius: 5px;
    }
    .complaint-evidence {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    .complaint-updates {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
    }
    .complaint-resolution {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }
    .update {
        margin-top: 15px;
        padding: 10px;
        border-radius: 4px;
        background-color: #f1f1f1;
    }
    .update h3 {
        margin: 0;
        color: #007BFF;
    }
</style>

<?php include './includes/sidebar.php'; ?>

<div class="content">
<div class="complaint-wrapper">
    <!-- Complaint Overview -->
    <div class="complaint-overview">
        <h2>Complaint Overview</h2>
        <h3>Complaint ID: <span id="complaint-id"><?php echo $complaint['id']; ?></span></h3>
        <h3>Department: <span id="complaint-department"><?php echo $complaint['department_title']; ?></span></h3>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
        <p><strong>Filed By:</strong> <?php echo htmlspecialchars($complaint['victim_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($complaint['victim_email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($complaint['victim_phone']); ?></p>
        <p><strong>Date Submitted:</strong> <?php echo date('F j, Y', strtotime($complaint['created_at'])); ?></p>
        <p><strong>Current Date:</strong> <span id="current-date"><?php echo date('F j, Y'); ?></span></p>
        <p><strong>Days Since Submission:</strong> <span id="days-passed"><?php echo $daysPassed . ' days'; ?></span></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($currentStatus); ?></p>
    </div>

    <!-- Complaint Updates -->
    <div class="complaint-updates">
        <h2>Status Updates</h2>
        <?php while ($update = $updatesResult->fetch_assoc()) : ?>
            <div class="update">
                <h3>Update on <?php echo date('F j, Y', strtotime($update['created_at'])); ?></h3>
                <p><strong>Handler:</strong> <?php echo htmlspecialchars($update['handler_name']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($update['update_status']); ?></p>
                <p><strong>Heading:</strong> <?php echo htmlspecialchars($update['heading']); ?></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($update['description'])); ?></p>

                <?php
                // Check if 'attached_doc' key exists and is not empty
                if (isset($update['attached_doc']) && !empty(trim($update['attached_doc']))) :
                ?>
                    <p><strong>Attached Document:</strong> <a href="<?php echo htmlspecialchars($update['attached_doc']); ?>" target="_blank">View Document</a></p>
                <?php else : ?>
                    <p>No attached document for this update.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        <?php if ($updatesResult->num_rows === 0) : ?>
            <p>No updates available for this complaint.</p>
        <?php endif; ?>
    </div>

    <!-- Complaint Resolution -->
    <!-- <div class="complaint-resolution">
        <h2>Resolution</h2>
        <p><em>This section will be updated when the issue is resolved.</em></p>
    </div> -->
</div>
</div>

<?php
// Close the database connections
$stmt->close();
$stmtUpdates->close();
$conn->close();
?>

<!-- Include the footer -->
<?php include './includes/footer.php'; ?>
