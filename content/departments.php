<?php
// Include header and database configuration
include '../admin/config.php';
include './includes/header.php';

// Fetch departments from the database
$sql_departments = "SELECT id, title, description, thumbnail, created_at FROM departments";
$result_departments = $conn->query($sql_departments);
?>

<div class="departments-container">
    <h1>Select a Department</h1>
    <p>Please select the relevant department to file your complaint. Click on the department that best matches the nature of your issue, and proceed to fill in the necessary details in the complaint form.</p>
    <div class="departments">
        <?php if ($result_departments->num_rows > 0): ?>
            <?php while ($department = $result_departments->fetch_assoc()): ?>
                <div class="department-card" onclick="goToComplaintForm('<?php echo htmlspecialchars($department['id']); ?>')">
                    <img src="../admin/<?php echo htmlspecialchars($department['thumbnail']); ?>" alt="<?php echo htmlspecialchars($department['title']); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($department['title']); ?></h3>
                        <p><?php echo htmlspecialchars($department['description']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No departments available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include './includes/footer.php'; ?>

<script>
function goToComplaintForm(departmentId) {
    // Redirect to the complaint form page with the department name
    window.location.href = './complaint_form.php?department=' + encodeURIComponent(departmentId);
}
</script>
