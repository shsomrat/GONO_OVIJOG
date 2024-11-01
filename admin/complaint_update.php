<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Check if 'role' is set in the session
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Fetch staff members for the dropdown
$staffQuery = "SELECT id, full_name FROM users WHERE role = 'staff'";
$staffResult = $conn->query($staffQuery);

// Get the complaint ID from the GET request
$complaint_id = (int)$_GET['complaint_id'];

// Fetch the latest handler_id for the complaint from the complaint_update table
$handlerQuery = "SELECT handler_id FROM complaint_update WHERE complaint_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($handlerQuery);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$handlerResult = $stmt->get_result();
$handlerData = $handlerResult->fetch_assoc();
$handler_id = $handlerData['handler_id'] ?? ''; // Default to empty if no handler_id is found
$stmt->close();

// Function to add a complaint update with attachment
function addComplaintUpdate($conn, $complaint_id, $status, $heading, $description, $handler_id, $attached_doc) {
    // Fetch complaint data to insert into the complaint_update table
    $complaintQuery = "SELECT title, department, description, created_at FROM complaints WHERE id = ?";
    $stmt = $conn->prepare($complaintQuery);
    $stmt->bind_param('i', $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaintData = $result->fetch_assoc();

    if ($complaintData) {
        // Insert into complaint_update table with all complaint data
        $sqlInsertUpdate = "INSERT INTO complaint_update (complaint_id, handler_id, complaint_title, department, complaint_description, complaint_created_at, status, heading, description, attached_doc)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsertUpdate);
        $stmtInsert->bind_param('iissssssss',
            $complaint_id,
            $handler_id,
            $complaintData['title'],
            $complaintData['department'],
            $complaintData['description'],
            $complaintData['created_at'],
            $status,
            $heading,
            $description,
            $attached_doc
        );

        if ($stmtInsert->execute()) {
            echo "Complaint update added successfully.";
        } else {
            echo "Error adding complaint update: " . $stmtInsert->error;
        }

        $stmtInsert->close();
    } else {
        echo "Complaint not found.";
    }

    $stmt->close();
}

// Process form submission to add a new complaint update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $heading = $_POST['heading'];
    $description = $_POST['description'];
    $handler_id = $_POST['handler_id'] ?: $handler_id; // Use submitted handler_id if available, otherwise use existing

    // Handle the file upload
    $attached_doc = '';
    if (isset($_FILES['attached_doc']) && $_FILES['attached_doc']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = './uploads/';
        $attached_doc = $uploadDir . basename($_FILES['attached_doc']['name']);
        if (move_uploaded_file($_FILES['attached_doc']['tmp_name'], $attached_doc)) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file.";
        }
    }

    // Call function to add a complaint update
    addComplaintUpdate($conn, $complaint_id, $status, $heading, $description, $handler_id, $attached_doc);
}

// Close the database connection
$conn->close();

include './includes/sidebar.php';
?>

<!-- HTML form for adding complaint updates -->
<div class="content">
    <h1>Add Complaint Update</h1>
    <form id="add-complaint-update-form" method="POST" action="" enctype="multipart/form-data">
        <label for="complaint_id">Complaint ID:</label>
        <input type="number" id="complaint_id" name="complaint_id" value="<?php echo htmlspecialchars($complaint_id); ?>" readonly required>

        <!-- Conditionally display handler selection for admin -->
        <?php if ($isAdmin): ?>
            <label for="handler_id">Select Handler:</label>
            <select name="handler_id" id="handler_id" required>
                <option value="">Select a staff member</option>
                <?php while ($staff = $staffResult->fetch_assoc()): ?>
                    <option value="<?php echo $staff['id']; ?>" <?php echo ($staff['id'] == $handler_id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($staff['full_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php else: ?>
            <!-- Hidden field for non-admin users -->
            <input type="hidden" name="handler_id" id="handler_id" value="<?php echo htmlspecialchars($handler_id); ?>">
        <?php endif; ?>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Approved">Approved</option>
            <option value="In Progress">In Progress</option>
            <option value="Under Review">Under Review</option>
            <option value="Resolved">Resolved</option>
            <option value="Rejected">Rejected</option>
        </select>

        <label for="heading">Heading:</label>
        <input type="text" id="heading" name="heading" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <label for="attached_doc">Attach Document (optional):</label>
        <input type="file" id="attached_doc" name="attached_doc">

        <button type="submit">Add Update</button>
    </form>
</div>

<!-- Include the footer -->
<?php include './includes/footer.php'; ?>
