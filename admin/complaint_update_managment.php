<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Check if a complaint ID is provided in the URL
if (isset($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    // Fetch the current complaint details from the `complaints` table
    $sql = "SELECT * FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $complaint = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    die("Complaint ID not provided.");
}

// Check if the form has been submitted to create a new record in `complaint_update`
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $heading = $_POST['heading'];
    $description = $_POST['description'];

    // Insert the update into the `complaint_update` table, including handler_id
    $sql = "INSERT INTO complaint_update (complaint_id, handler_id, status, heading, description)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Use the handler_id from the complaint
    $handler_id = $complaint['handler_id'];

    $stmt->bind_param("iisss", $complaint_id, $handler_id, $status, $heading, $description);
    if ($stmt->execute()) {
        // Update the complaint status in the `complaints` table
        $updateComplaint = "UPDATE complaints SET status = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateComplaint);
        $stmtUpdate->bind_param("si", $status, $complaint_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Redirect with a success message
        header("Location: complaint_update.php?complaint_id=$complaint_id&updated=1");
        exit();
    } else {
        echo "<p style='color: red;'>Failed to update complaint.</p>";
    }
    $stmt->close();
}
?>

<?php
// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <h1>Update Complaint</h1>

    <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
        <p style="color: green;">Complaint updated successfully.</p>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="In Progress" <?php echo ($complaint['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                <option value="Under Review" <?php echo ($complaint['status'] == 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
                <option value="Resolved" <?php echo ($complaint['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
        <div>
            <label for="heading">Update Heading</label>
            <input type="text" name="heading" id="heading" required>
        </div>
        <div>
            <label for="description">Update Description</label>
            <textarea name="description" id="description" rows="5" required></textarea>
        </div>
        <button type="submit">Submit Update</button>
    </form>
</div>

<script>
    function viewCase(id) {
        location.href = `complaint_details.php?id=${id}`;
    }
</script>

<?php
// Include the footer
include './includes/footer.php';
?>
