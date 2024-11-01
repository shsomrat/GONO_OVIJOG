<div class="sidebar">
    <img src="./assets/img/logo.png" alt="Organization Logo"> <!-- Add your logo image path here -->
        <!-- Conditional title based on user role -->
    <?php if ($_SESSION['role'] === 'admin'): ?>
        <h2 class="admin-menu">Admin Panel</h2>
    <?php else: ?>
        <h2 class="admin-menu">Sub Admin Panel</h2>
    <?php endif; ?>
    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i>Dashboard</a>
    <a href="add_new_complaint.php?user_id=<?php echo $_SESSION['user_id']?>"><i class="fa-solid fa-file-pen"></i>Add New Complaint</a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="pending_complaints.php"><i class="fa-solid fa-file-waveform"></i>Pending Complaints</a>
            <a href="complaints_management.php"><i class="fa-solid fa-eye"></i>View Complaints</a>
            <!-- <a href="complaint_update_managment.php"><i class="fa-solid fa-eye"></i>All Updates</a> -->
            <a href="reports_and_analitics.php"><i class="fa-solid fa-chart-line"></i>Reports and Analytics</a>
            <a href="departments_management.php" class="active"><i class="fa-solid fa-sitemap"></i>Department Management</a>
            <a href="users_management.php"><i class="fa-solid fa-user-group"></i>User Management</a>
            <!-- <a href="status_managment.php"><i class="fa-solid fa-signal"></i>Status Management</a> -->
            <!-- Add more links as needed -->
        <?php elseif ($_SESSION['role'] === 'staff'): ?>
            <a href="complaints_management.php"><i class="fa-solid fa-eye"></i>View Complaints</a>
            <!-- <a href="complaint_update_managment.php"><i class="fa-solid fa-eye"></i>All Updates</a> -->
            <!-- <a href="reports_and_analitics.php"><i class="fa-solid fa-chart-line"></i>Reports and Analytics</a> -->
            <!-- <a href="departments_management.php" class="active"><i class="fa-solid fa-sitemap"></i>Department Management</a> -->
            <!-- <a href="users_management.php"><i class="fa-solid fa-user-group"></i>User Management</a> -->
            <!-- <a href="status_managment.php"><i class="fa-solid fa-signal"></i>Status Management</a> -->
            <!-- Add more links as needed -->
            <?php endif; ?>
            <!-- Log Out button -->
    <h2><a href="../content/index.php" class="admin-menu view-site">View Site <i class="fa-solid fa-arrow-up-right-from-square"></i></a></h2>
    <a href="logout.php" class="logout-btn"><i class="fa-solid fa-sign-out-alt"></i> Log Out</a> <!-- Link to the logout script -->
</div>
