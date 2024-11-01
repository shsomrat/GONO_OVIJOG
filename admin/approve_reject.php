<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Include the sidebar
include './includes/sidebar.php';

// Fetch the complaint details based on the ID passed in the URL
if (isset($_GET['complaint_id'])) {
    $complaint_id = (int)$_GET['complaint_id'];
    // Prepare the query to fetch complaint details
    $complaintQuery = "SELECT * FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($complaintQuery);
    $stmt->bind_param('i', $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaint = $result->fetch_assoc();

    // If no complaint found, show an error message
    if (!$complaint) {
        echo "Complaint not found.";
        exit;
    }
} else {
    echo "No complaint ID provided.";
    exit;
}

// Fetch the staff members for the handler selection
$staffQuery = "SELECT id, full_name FROM users WHERE role = 'staff'";
$staffResult = $conn->query($staffQuery);
?>

<div class="content">
    <h2>Approve or Reject Complaint ID: <?php echo htmlspecialchars($complaint['id']); ?></h2>

    <!-- Form to approve or reject the complaint -->
    <form method="POST" action="add_complaint_updates.php">
        <!-- Hidden inputs to pass important details -->
        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['id']); ?>">
        <input type="hidden" name="complaint_title" value="<?php echo htmlspecialchars($complaint['title']); ?>">
        <input type="hidden" name="department" value="<?php echo htmlspecialchars($complaint['department']); ?>">
        <input type="hidden" name="complaint_description" value="<?php echo htmlspecialchars($complaint['description']); ?>">
        <input type="hidden" name="complaint_created_at" value="<?php echo htmlspecialchars($complaint['created_at']); ?>">

        <label>Complaint Title:</label>
        <p><?php echo htmlspecialchars($complaint['title']); ?></p>

        <label>Department:</label>
        <p><?php echo htmlspecialchars($complaint['department']); ?></p>

        <label>Filled By:</label>
        <p><?php echo htmlspecialchars($complaint['filled_by']); ?></p>

        <label>Victim Name:</label>
        <p><?php echo htmlspecialchars($complaint['victim_name']); ?></p>

        <label>Victim Email:</label>
        <p><?php echo htmlspecialchars($complaint['victim_email']); ?></p>

        <label>Victim Phone:</label>
        <p><?php echo htmlspecialchars($complaint['victim_phone']); ?></p>

        <label>Attached Document:</label>
        <p><a href="<?php echo htmlspecialchars($complaint['attached_doc']); ?>" target="_blank">View Document</a></p>

        <label>Description:</label>
        <p><?php echo htmlspecialchars($complaint['description']); ?></p>

        <label>Date Submitted:</label>
        <p><?php echo htmlspecialchars($complaint['created_at']); ?></p>

        <!-- Dropdown to select the handler -->
        <label for="handler_id">Select Handler:</label>
        <select name="handler_id" id="handler_id" required>
            <option value="">Select a staff member</option>
            <?php while ($staff = $staffResult->fetch_assoc()): ?>
                <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['full_name']); ?></option>
            <?php endwhile; ?>
        </select>

        <!-- Dropdown to select the status -->
        <label for="status">Status:</label>
        <select name="status" id="status" required>
        <option value="approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>

        <!-- Input for heading -->
        <label for="heading">Heading:</label>
        <input type="text" name="heading" id="heading" required>

        <!-- Textarea for the reason or description -->
        <label for="description">Reason:</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <!-- Submit button -->
        <button type="submit">Submit Decision</button>
    </form>
</div>

<!-- Include the footer -->
<?php include './includes/footer.php'; ?>
