<?php
include './includes/header.php';
include '../admin/config.php';

// Retrieve complaint ID from the URL
if (isset($_GET['complaint_id'])) {
    $complaint_id = (int)$_GET['complaint_id'];

    // Fetch complaint details from the database
    $stmt = $conn->prepare("SELECT title, description, created_at FROM complaints WHERE id = ?");
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $stmt->bind_result($title, $description, $created_at);
    $stmt->fetch();
    $stmt->close();

    // Format the date and time
    $submission_date = date("Y-m-d", strtotime($created_at));
    $submission_time = date("H:i:s", strtotime($created_at));
} else {
    echo "No complaint ID found.";
    exit();
}
?>

<div class="long-container confirmation-container">
    <div class="success-container">
        <h2>Success! Your Complaint Has Been Submitted!</h2>
        <p>Thank you, your complaint has been submitted successfully.</p>
    </div>

    <p><strong>Complaint ID:</strong> <?php echo $complaint_id; ?></p>
    <p><strong>Submission Date:</strong> <?php echo $submission_date; ?></p>
    <p><strong>Submission Time:</strong> <?php echo $submission_time; ?></p>

    <!-- Display PDF content -->
    <div id="pdf-container">
        <p id="name-display"><?php echo htmlspecialchars($title); ?></p>
        <p id="details-display"><?php echo htmlspecialchars($description); ?></p>
    </div>

    <!-- PDF Download Button -->
    <button onclick="downloadPDF()">Download Receipt as PDF</button>
</div>

<?php
include './includes/footer.php';
?>
