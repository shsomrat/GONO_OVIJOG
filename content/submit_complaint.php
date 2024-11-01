<?php
// Start session to access session variables
session_start();

// Include database configuration
include '../admin/config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $title = trim($_POST['title']);
    $department = (int)trim($_POST['department']); // Ensure department is an integer
    $victimName = trim($_POST['victim_name']);
    $victimEmail = trim($_POST['victim_email']);
    $victimPhone = trim($_POST['victim_phone']);
    $description = trim($_POST['description']);
    $attachedDoc = ''; // Initialize file path variable
    $filled_by = $_SESSION['user_id']; // Get user ID from session

    // Handle file upload
    if (isset($_FILES['attached_doc']) && $_FILES['attached_doc']['error'] == UPLOAD_ERR_OK) {
        $targetDirectory = "../uploads/"; // Adjust path
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
        // Prepare SQL to insert data into the 'complaints' table
        $stmt = $conn->prepare("INSERT INTO complaints (title, department, filled_by, status, victim_name, victim_email, victim_phone, attached_doc, description) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)");

        // Bind parameters correctly - 9 parameters in total ('s' for string, 'i' for integer)
        $stmt->bind_param("sissssss", $title, $department, $filled_by, $victimName, $victimEmail, $victimPhone, $attachedDoc, $description);

        if ($stmt->execute()) {
            $complaint_id = $conn->insert_id; // Get the last inserted ID
            header("Location: success.php?complaint_id=" . $complaint_id);
            exit();
        } else {
            echo "Error: " . $stmt->error; // Debugging line
        }

        $stmt->close();
    }
} else {
    // Redirect back to the form if accessed directly
    header("Location: ../admin/add_new_complaint.php");
    exit();
}
?>
