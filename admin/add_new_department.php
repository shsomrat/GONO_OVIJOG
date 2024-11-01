<?php

// Include data_table.php to create tables if they do not exist
include './data_table.php';

// Include the header
include './includes/header.php';

// Include database configuration
include './config.php';



// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $title = trim($_POST['department-title']);
    $description = trim($_POST['department-description']);
    $thumbnail = $_FILES['department-thumbnail'];

    // Validate the form fields
    if (empty($title) || empty($description) || $thumbnail['error'] != UPLOAD_ERR_OK) {
        echo "<p>Please fill in all fields and upload a valid image.</p>";
    } else {
        // Handle the file upload
        $targetDirectory = "uploads/";
        $targetFile = $targetDirectory . basename($thumbnail['name']);
        $uploadOk = 1;

        // Check if the file is an actual image
        $check = getimagesize($thumbnail['tmp_name']);
        if ($check === false) {
            echo "<p>File is not an image.</p>";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            echo "<p>Sorry, file already exists.</p>";
            $uploadOk = 0;
        }

        // Move the uploaded file to the target directory
        if ($uploadOk && move_uploaded_file($thumbnail['tmp_name'], $targetFile)) {
            // Insert the department data into the database
            $stmt = $conn->prepare("INSERT INTO departments (title, description, thumbnail) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $description, $targetFile);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Department added successfully!";
            } else {
                $_SESSION['message'] = "Error adding department: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "<p>Sorry, there was an error uploading your file.</p>";
        }
    }
}

// Close the database connection (optional here since the script will end)
$conn->close();
?>

<?php
// Include the sidebar (if needed)
include './includes/sidebar.php';
?>

<div class="content">
    <div>
       <?php if (isset($_SESSION['message'])): ?>
                <p><?php echo $_SESSION['message']; ?></p>
                <?php unset($_SESSION['message']); // Clear message after displaying it ?>
            <?php endif; ?>
    </div>
    <h1>Add New Department</h1>
    <form id="add-department-form" method="POST" action="" enctype="multipart/form-data">
        <input type="text" name="department-title" placeholder="Department Title" required>
        <textarea name="department-description" placeholder="Department Description" required></textarea>
        <input type="file" name="department-thumbnail" accept="image/*" required>
        <button class="add_new" type="submit">Add Department</button>
    </form>
</div>

<?php
// Include the footer
include './includes/footer.php';
?>
