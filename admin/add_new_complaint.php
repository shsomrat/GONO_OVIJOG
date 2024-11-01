<?php
// Include data_table.php to create tables if they do not exist
include './data_table.php';

// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';


// Fetch department options from 'departments' table
$departmentResult = $conn->query("SELECT id, title FROM departments");
$departmentOptions = [];
if ($departmentResult->num_rows > 0) {
    while ($row = $departmentResult->fetch_assoc()) {
        $departmentOptions[] = $row;
    }
}

// Get filled_by from session
$filled_by = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $department = trim($_POST['department']);
    $victimName = trim($_POST['victim_name']);
    $victimEmail = trim($_POST['victim_email']);
    $victimPhone = trim($_POST['victim_phone']);
    $description = trim($_POST['description']);
    $attachedDoc = ''; // Initialize file path variable

    // Handle the file upload
    if (isset($_FILES['attached_doc']) && $_FILES['attached_doc']['error'] == UPLOAD_ERR_OK) {
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($_FILES['attached_doc']['name']);

        // Validate file type and size
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

        if (in_array($fileType, $allowedTypes) && $_FILES['attached_doc']['size'] < 5000000) { // Limit size to 5MB
            if (move_uploaded_file($_FILES['attached_doc']['tmp_name'], $targetFile)) {
                $attachedDoc = $targetFile; // Save the file path
            } else {
                echo "Error uploading the file.";
            }
        } else {
            echo "Invalid file type or size exceeded.";
        }
    }

    // Validate required fields
    if (empty($title) || empty($department) || empty($victimName) || empty($victimEmail) || empty($victimPhone) || empty($description)) {
        echo "Please fill in all the fields.";
    } else {
        // Insert the data into the 'complaints' table
        $stmt = $conn->prepare("INSERT INTO complaints (title, department, filled_by, status, victim_name, victim_email, victim_phone, attached_doc, description) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)");

        // Ensure that all the parameters match the correct number
        $stmt->bind_param("siisssss", $title, $department, $filled_by, $victimName, $victimEmail, $victimPhone, $attachedDoc, $description);

        if ($stmt->execute()) {
            header("Location: complaints_management.php?success=1"); // Redirect after success
            exit();
        } else {
            echo "Error: " . $stmt->error; // Debugging line
        }

        $stmt->close();
    }
}

// Fetch users for handler dropdown (optional)
$userResult = $conn->query("SELECT id, full_name FROM users"); // Adjust as needed

// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<!-- HTML form for adding complaints -->
<div class="content">
    <h1>Add New Complaint</h1>
    <form id="add-complaint-form" method="POST" action="" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="department">Department:</label>
        <select id="department" name="department" required>
            <option value="">Select Department</option>
            <?php foreach ($departmentOptions as $option): ?>
                <option value="<?php echo $option['id']; ?>"><?php echo $option['title']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="victim_name">Victim Name:</label>
        <input type="text" id="victim_name" name="victim_name" required>

        <label for="victim_email">Victim Email:</label>
        <input type="email" id="victim_email" name="victim_email" required>

        <label for="victim_phone">Victim Phone:</label>
        <input type="tel" id="victim_phone" name="victim_phone" required>

        <label for="attached_doc">Attach Document (optional):</label>
        <input type="file" id="attached_doc" name="attached_doc" accept=".pdf, .doc, .docx, .jpg, .png">

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <button type="submit">Add Complaint</button>
    </form>
</div>

<!-- Include the footer -->
<?php include './includes/footer.php'; ?>
