<?php
// Include database configuration and header
include '../admin/config.php';
include './includes/header.php';

// Check if user is logged in and retrieve user data
if (!isset($_SESSION['user_id'])) {
    echo "<h3>Please log in to view this page.</h3>";
    include './includes/footer.php';
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();

// Fetch user complaints from the database
$sql_complaints = "SELECT * FROM complaints WHERE filled_by = ?";
$stmt_complaints = $conn->prepare($sql_complaints);
$stmt_complaints->bind_param("i", $user_id);
$stmt_complaints->execute();
$complaintsResult = $stmt_complaints->get_result();
?>

<section class="user-dashboard">
    <div class="container">
        <div class="dashboard-wrapper">
            <aside class="dashboard-menu">
                <h3>User Dashboard</h3>
                <ul>
                    <li class="active"><a href="#profile-overview">Profile Overview</a></li>
                    <li><a href="#personal-info">Personal Information</a></li>
                    <li><a href="#edit-profile">Edit Profile</a></li>
                    <li><a href="#my-complaints">My Complaints</a></li>
                    <li><a href="#complaint-status">Status Updates</a></li>
                </ul>
            </aside>

            <div class="dashboard-content">
                <!-- Profile Overview -->
                <section id="profile-overview">
                    <h2>Profile Overview</h2>
                    <?php if ($userResult): ?>
                        <p>Welcome, <strong><?php echo htmlspecialchars($userResult['full_name']); ?></strong>! Here's a quick overview of your account.</p>
                        <div class="profile-details">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($userResult['full_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($userResult['email']); ?></p>
                            <p><strong>Joined:</strong> <?php echo htmlspecialchars($userResult['created_at']); ?></p>
                        </div>
                    <?php else: ?>
                        <p>User details are not available.</p>
                    <?php endif; ?>
                </section>

                <!-- Personal Information -->
                <section id="personal-info">
                    <h2>Personal Information</h2>
                    <?php if ($userResult): ?>
                        <div class="info-details">
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($userResult['phone'] ?? 'Not provided'); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($userResult['address'] ?? 'Not provided'); ?></p>
                            <p><strong>Occupation:</strong> <?php echo htmlspecialchars($userResult['occupation'] ?? 'Not provided'); ?></p>
                        </div>
                    <?php else: ?>
                        <p>Personal information is not available.</p>
                    <?php endif; ?>
                </section>

                <!-- Edit Profile -->
                <section id="edit-profile">
                    <h2>Edit Profile</h2>
                    <?php if ($userResult): ?>
                        <form action="update_profile.php" method="POST">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="full_name" value="<?php echo htmlspecialchars($userResult['full_name']); ?>" required>

                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userResult['email']); ?>" required>

                            <label for="phone">Phone:</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userResult['phone'] ?? ''); ?>">

                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userResult['address'] ?? ''); ?>">

                            <button class="save-change" type="submit">Save Changes</button>
                        </form>
                    <?php else: ?>
                        <p>Unable to load profile for editing.</p>
                    <?php endif; ?>
                </section>

                <!-- My Complaints -->
                <section id="my-complaints">
                    <h2>My Complaints</h2>
                    <ul class="complaints-list">
                        <?php if ($complaintsResult->num_rows > 0): ?>
                            <?php while ($complaint = $complaintsResult->fetch_assoc()):
                                $complaint_id = $complaint['id'];
                                $sql_latest_update = "SELECT status FROM complaint_update WHERE complaint_id = ? ORDER BY created_at DESC LIMIT 1";
                                $stmt_latest_update = $conn->prepare($sql_latest_update);
                                $stmt_latest_update->bind_param("i", $complaint_id);
                                $stmt_latest_update->execute();
                                $latestUpdateResult = $stmt_latest_update->get_result();
                                $status = $latestUpdateResult->num_rows > 0
                                    ? $latestUpdateResult->fetch_assoc()['status']
                                    : $complaint['status'];
                            ?>
                                <li>
                                    <strong>Complaint ID:</strong> <?php echo htmlspecialchars($complaint_id); ?>
                                    <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
                                    <a href="complaint_details.php?id=<?php echo $complaint_id; ?>">View Details</a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No complaints found.</li>
                        <?php endif; ?>
                    </ul>
                </section>

                <!-- Status Updates -->
                <section id="complaint-status">
                    <h2>Status Updates</h2>
                    <p>Updates on the status of your submitted complaints.</p>
                    <ul class="status-updates">
                        <?php if ($complaintsResult->num_rows > 0): ?>
                            <?php while ($complaint = $complaintsResult->fetch_assoc()):
                                $complaint_id = $complaint['id'];
                                $sql_updates = "SELECT * FROM complaint_update WHERE complaint_id = ?";
                                $stmt_updates = $conn->prepare($sql_updates);
                                $stmt_updates->bind_param("i", $complaint_id);
                                $stmt_updates->execute();
                                $updatesResult = $stmt_updates->get_result();
                                while ($update = $updatesResult->fetch_assoc()): ?>
                                    <li>
                                        <strong>Complaint ID:</strong> <?php echo htmlspecialchars($complaint_id); ?>
                                        <p><strong>Status:</strong> <?php echo htmlspecialchars($update['status']); ?></p>
                                        <p><strong>Update Date:</strong> <?php echo htmlspecialchars($update['created_at']); ?></p>
                                        <a href="complaint_details.php?id=<?php echo $complaint_id; ?>">View Details</a>
                                    </li>
                                <?php endwhile; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No status updates available.</li>
                        <?php endif; ?>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</section>

<?php include './includes/footer.php'; ?>
