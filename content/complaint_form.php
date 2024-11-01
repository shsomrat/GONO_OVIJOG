<?php
include './includes/header.php';
include '../admin/config.php';

// Check if the 'department' parameter is set in the URL
$selectedDepartment = isset($_GET['department']) ? (int)$_GET['department'] : null;
?>

<section class="complaint-form">
  <div class="complaint-from-container">
    <h1>Submit Your Complaint</h1>
    <form id="complaintForm" action="submit_complaint.php" method="POST" enctype="multipart/form-data">

      <!-- Personal Information -->
      <div class="form-group">
        <label for="victim_name">Full Name</label>
        <input type="text" id="victim_name" name="victim_name" required>
      </div>

      <div class="form-group">
        <label for="victim_email">Email Address</label>
        <input type="email" id="victim_email" name="victim_email" required>
      </div>

      <div class="form-group">
        <label for="victim_phone">Phone Number</label>
        <input type="tel" id="victim_phone" name="victim_phone" required>
      </div>

      <div class="form-group">
        <label for="department">Department</label>
        <select id="department" name="department" required>
            <?php
            // Fetch departments from the database
            $departmentResult = $conn->query("SELECT id, title FROM departments");
            if ($departmentResult->num_rows > 0) {
                while ($row = $departmentResult->fetch_assoc()) {
                    // Check if this department matches the selected department from the URL
                    $isSelected = $selectedDepartment === (int)$row['id'] ? 'selected' : '';
                    echo "<option value=\"{$row['id']}\" $isSelected>{$row['title']}</option>";
                }
            } else {
                echo "<option value=\"\">No departments available</option>";
            }
            ?>
        </select>
      </div>

      <!-- Complaint Details -->
      <div class="form-group">
        <label for="title">Complaint Title</label>
        <input type="text" id="title" name="title" required>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5" required></textarea>
      </div>

      <!-- Document Upload -->
      <div class="form-group">
        <label for="attached_doc">Upload Supporting Documents (optional)</label>
        <input type="file" id="attached_doc" name="attached_doc" accept=".pdf, .doc, .docx, .jpg, .png">
        <p class="file-info">Allowed formats: PDF, DOC, DOCX, JPG, PNG (Max size: 5MB)</p>
      </div>

      <!-- Submit Button -->
      <div class="form-group">
        <button type="submit" class="submit-btn">Submit Your Complaint</button>
      </div>

    </form>
  </div>
</section>

<?php
include './includes/footer.php';
?>

