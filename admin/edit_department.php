<?php
// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';

// Check if department ID is provided in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch department data from the database
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $department = $result->fetch_assoc();
    $stmt->close();

    if (!$department) {
        echo "<p>Department not found.</p>";
        exit();
    }
} else {
    echo "<p>No department ID provided.</p>";
    exit();
}

// Process form submission for updating the department
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['department-title']);
    $description = trim($_POST['department-description']);
    $thumbnail = $_FILES['department-thumbnail'];

    // Validate the form fields
    if (empty($title) || empty($description)) {
        echo "<p>Please fill in all fields.</p>";
    } else {
        $thumbnailPath = $department['thumbnail'];

        // Handle file upload if a new image is provided
        if ($thumbnail['error'] == UPLOAD_ERR_OK) {
            $targetDirectory = "uploads/";
            $targetFile = $targetDirectory . basename($thumbnail['name']);

            // Check if the file is an actual image
            $check = getimagesize($thumbnail['tmp_name']);
            if ($check === false) {
                echo "<p>File is not an image.</p>";
            } elseif (move_uploaded_file($thumbnail['tmp_name'], $targetFile)) {
                $thumbnailPath = $targetFile;
            } else {
                echo "<p>Sorry, there was an error uploading your file.</p>";
            }
        }

        // Update department data in the database
        $stmt = $conn->prepare("UPDATE departments SET title = ?, description = ?, thumbnail = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $thumbnailPath, $id);

        if ($stmt->execute()) {
            echo "<p>Department updated successfully!</p>";
        } else {
            echo "<p>Error updating department: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
}

// Close the database connection (optional here)
$conn->close();

// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <h1>Update Department</h1>
    <form id="update-department-form" method="POST" action="" enctype="multipart/form-data">
        <input type="text" name="department-title" placeholder="Department Title" value="<?php echo htmlspecialchars($department['title']); ?>" required>
        <textarea name="department-description" placeholder="Department Description" required><?php echo htmlspecialchars($department['description']); ?></textarea>
        <input type="file" name="department-thumbnail" accept="image/*">
        <button type="submit">Update Department</button>
    </form>
</div>

<?php
// Include the footer
include './includes/footer.php';
?>
