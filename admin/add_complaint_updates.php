<?php

// Include data_table.php to create tables if they do not exist
include './data_table.php';

// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';


// Check if 'role' is set in the session
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';


// Function to add a complaint update
function addComplaintUpdate($conn, $complaint_id, $handler_id, $status, $heading, $description, $attachedDoc) {
    // Fetch complaint data to insert into the complaint_update table
    $complaintQuery = "SELECT title, department, description, created_at
                       FROM complaints
                       WHERE id = ?";
    $stmt = $conn->prepare($complaintQuery);
    $stmt->bind_param('i', $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaintData = $result->fetch_assoc();

    if ($complaintData) {
        // Prepare to insert into complaint_update table with all complaint data
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
            $attachedDoc
        );

        if ($stmtInsert->execute()) {
            // Redirect to pending_complaints.php after successful insert
            header("Location: pending_complaints.php?success=1");
            exit();
        } else {
            echo "Error adding complaint update: " . $stmtInsert->error;
        }

        $stmtInsert->close();
    } else {
        echo "Complaint not found.";
    }

    $stmt->close();
}

// Sample form handling to add a new complaint update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complaint_id = (int)$_POST['complaint_id'];
    $handler_id = (int)$_POST['handler_id'];
    $status = $_POST['status'];
    $heading = $_POST['heading'];
    $description = $_POST['description'];
    $attachedDoc = '';

    // Handle file upload if an attachment is provided
    if (!empty($_FILES['attached_doc']['name'])) {
        $targetDir = "uploads/";
        $attachedDoc = $targetDir . basename($_FILES['attached_doc']['name']);
        if (!move_uploaded_file($_FILES['attached_doc']['tmp_name'], $attachedDoc)) {
            echo "Error uploading file.";
        }
    }

    // Call function to add a complaint update
    addComplaintUpdate($conn, $complaint_id, $handler_id, $status, $heading, $description, $attachedDoc);
}

// Close the database connection
$conn->close();

include './includes/sidebar.php';
?>

<div class="content">
    <h1>Add Complaint Update</h1>
    <form id="add-complaint-update-form" method="POST" action="" enctype="multipart/form-data">
        <label for="complaint_id">Complaint ID:</label>
        <input type="number" id="complaint_id" name="complaint_id" value="<?php echo htmlspecialchars($complaint_id); ?>" readonly required>

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
        <input type="file" id="attached_doc" name="attached_doc" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

        <button type="submit">Add Update</button>
    </form>
</div>

<!-- Include the footer -->
<?php include './includes/footer.php'; ?>
