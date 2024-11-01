<?php
// Include header and database configuration
include '../admin/config.php';
include './includes/header.php';

// Check if complaint ID is provided in the URL
if (!isset($_GET['id'])) {
    echo "<h3>Complaint ID is missing.</h3>";
    include './includes/footer.php';
    exit();
}

$complaint_id = $_GET['id'];

// Fetch complaint details from the complaints table
$sql_complaint = "
    SELECT
        c.id, c.title, c.department, c.filled_by, c.status,
        c.victim_name, c.victim_email, c.victim_phone,
        c.attached_doc, c.description, c.created_at,
        d.title AS department_title,
        u.full_name AS filed_by_name
    FROM complaints c
    LEFT JOIN departments d ON c.department = d.id
    LEFT JOIN users u ON c.filled_by = u.id
    WHERE c.id = ?";

$stmt = $conn->prepare($sql_complaint);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();

// Calculate days since submission
$created_at = new DateTime($complaint['created_at']);
$current_date = new DateTime();
$days_passed = $current_date->diff($created_at)->days;

// Fetch the latest status from complaint_update if any updates are present
$sql_latest_status = "
    SELECT status
    FROM complaint_update
    WHERE complaint_id = ?
    ORDER BY created_at DESC
    LIMIT 1";
$stmt_latest_status = $conn->prepare($sql_latest_status);
$stmt_latest_status->bind_param("i", $complaint_id);
$stmt_latest_status->execute();
$latest_status_result = $stmt_latest_status->get_result();
$latest_status = $latest_status_result->fetch_assoc();

// Determine which status to display: latest status or initial complaint status
$display_status = $latest_status ? $latest_status['status'] : $complaint['status'];

// Fetch all updates for the complaint from the complaint_update table
$sql_updates = "SELECT heading, description, status, created_at FROM complaint_update WHERE complaint_id = ? ORDER BY created_at ASC";
$stmt_updates = $conn->prepare($sql_updates);
$stmt_updates->bind_param("i", $complaint_id);
$stmt_updates->execute();
$updatesResult = $stmt_updates->get_result();
?>

<section class="complaint-details">
    <div class="complaint-details-container">
        <div class="complaint-wrapper">
            <!-- Complaint Overview -->
            <div class="complaint-overview">
                <h2>Complaint ID: <span id="complaint-id"><?php echo htmlspecialchars($complaint['id']); ?></span></h2>
                <h2>Complaint Department: <span id="complaint-department"><?php echo htmlspecialchars($complaint['department_title']); ?></span></h2>
                <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
                <p><strong>Filed By:</strong> <?php echo htmlspecialchars($complaint['filed_by_name']); ?></p>
                <p><strong>Victim Name:</strong> <?php echo htmlspecialchars($complaint['victim_name']); ?></p>
                <p><strong>Victim Email:</strong> <?php echo htmlspecialchars($complaint['victim_email']); ?></p>
                <p><strong>Victim Phone:</strong> <?php echo htmlspecialchars($complaint['victim_phone']); ?></p>
                <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars($complaint['created_at']); ?></p>
                <p><strong>Current Date:</strong> <span id="current-date"><?php echo $current_date->format('Y-m-d'); ?></span></p>
                <p><strong>Days Since Submission:</strong> <span id="days-passed"><?php echo $days_passed; ?> days</span></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($display_status); ?></p>
            </div>

            <!-- Complaint Details -->
            <div class="complaint-description">
                <h2>Description</h2>
                <p><?php echo htmlspecialchars($complaint['description']); ?></p>
            </div>

            <!-- Supporting Evidence -->
            <?php if (!empty($complaint['attached_doc'])): ?>
                <div class="complaint-evidence">
                    <h2>Supporting Evidence</h2>
                    <ul>
                        <li><a href="<?php echo htmlspecialchars($complaint['attached_doc']); ?>">Download Document</a></li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Complaint Updates -->
            <div class="complaint-updates">
                <h2>Status Updates</h2>
                <?php while ($update = $updatesResult->fetch_assoc()): ?>
                    <div class="update">
                        <h3><?php echo htmlspecialchars($update['heading']); ?> (<?php echo htmlspecialchars($update['created_at']); ?>)</h3>
                        <p><?php echo htmlspecialchars($update['description']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($update['status']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Complaint Resolution -->
            <!-- <div class="complaint-resolution">
                <h2>Resolution</h2>
                <p><em>This section will be updated when the issue is resolved.</em></p>
            </div> -->

            <!-- PDF Download Button -->
            <button onclick="downloadComplaintPDF()">Download Complaint Details as PDF</button>
        </div>
    </div>
</section>

<?php
include './includes/footer.php';
?>
