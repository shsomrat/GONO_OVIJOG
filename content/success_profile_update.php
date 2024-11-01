<?php
// Check for the 'status' query parameter in the URL
$statusMessage = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $statusMessage = "<p class='success-message'>Profile updated successfully!</p>";
    } elseif ($_GET['status'] === 'error') {
        $statusMessage = "<p class='error-message'>An error occurred while updating the profile. Please try again.</p>";
    }
}
include './includes/header.php';
?>


<section id="edit-profile">
  <div class="long-container container">
      <!-- Display the status message if it exists -->
      <?php echo $statusMessage; ?>
  </div>
</section>

<?php include './includes/footer.php'; ?>