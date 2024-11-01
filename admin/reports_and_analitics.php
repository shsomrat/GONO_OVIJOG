<?php
// Include the header and sidebar
include './includes/header.php';
include './includes/sidebar.php';

// Database connection
include './config.php';

// Fetch complaint summary data
$summaryQuery = "SELECT complaint_id, handler_id, complaint_title, department, complaint_created_at, status FROM complaint_update";
$summaryResult = mysqli_query($conn, $summaryQuery);

// Fetch data for charts
// Complaints by Status
$statusQuery = "SELECT status, COUNT(*) as count FROM complaint_update GROUP BY status";
$statusResult = mysqli_query($conn, $statusQuery);
$statusData = [];
$statusLabels = [];
while ($row = mysqli_fetch_assoc($statusResult)) {
    $statusLabels[] = $row['status'];
    $statusData[] = $row['count'];
}

// Complaints Over Time (using created_at field to group by date)
$timeQuery = "SELECT DATE(complaint_created_at) as date, COUNT(*) as count FROM complaint_update GROUP BY DATE(complaint_created_at)";
$timeResult = mysqli_query($conn, $timeQuery);
$timeLabels = [];
$timeData = [];
while ($row = mysqli_fetch_assoc($timeResult)) {
    $timeLabels[] = $row['date'];
    $timeData[] = $row['count'];
}

// Complaints by Department
$departmentQuery = "SELECT department, COUNT(*) as count FROM complaint_update GROUP BY department";
$departmentResult = mysqli_query($conn, $departmentQuery);
$departmentLabels = [];
$departmentData = [];
while ($row = mysqli_fetch_assoc($departmentResult)) {
    $departmentLabels[] = $row['department'];
    $departmentData[] = $row['count'];
}
?>

<div class="content">
    <div class="card">
        <h2>Reports and Analytics</h2>
        <div class="charts">
            <div class="chart">
                <h3>Complaints by Status</h3>
                <canvas id="complaintsChart"></canvas>
            </div>
            <div class="chart">
                <h3>Complaints Over Time</h3>
                <canvas id="timeChart"></canvas>
            </div>
            <div class="chart">
                <h3>Complaints by Department</h3>
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Complaint Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Filed By</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($summaryResult)) : ?>
                    <tr>
                        <td><?php echo $row['complaint_id']; ?></td>
                        <td><?php echo $row['handler_id']; ?></td>
                        <td><?php echo date("F j, Y", strtotime($row['complaint_created_at'])); ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['department']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data for Complaints by Status
const complaintsStatusData = {
    labels: <?php echo json_encode($statusLabels); ?>,
    datasets: [{
        label: 'Number of Complaints',
        data: <?php echo json_encode($statusData); ?>,
        backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
    }]
};

// Data for Complaints Over Time
const timeData = {
    labels: <?php echo json_encode($timeLabels); ?>,
    datasets: [{
        label: 'Complaints Over Time',
        data: <?php echo json_encode($timeData); ?>,
        borderColor: '#007bff',
        fill: false,
        tension: 0.1
    }]
};

// Data for Complaints by Department
const departmentData = {
    labels: <?php echo json_encode($departmentLabels); ?>,
    datasets: [{
        label: 'Number of Complaints',
        data: <?php echo json_encode($departmentData); ?>,
        backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
    }]
};

// Configurations for each chart
const complaintsChartConfig = {
    type: 'bar',
    data: complaintsStatusData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Complaints by Status'
            }
        }
    }
};

const timeChartConfig = {
    type: 'line',
    data: timeData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Complaints Over Time'
            }
        }
    }
};

const departmentChartConfig = {
    type: 'doughnut',
    data: departmentData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Complaints by Department'
            }
        }
    }
};

// Render the charts
const complaintsChart = new Chart(document.getElementById('complaintsChart'), complaintsChartConfig);
const timeChart = new Chart(document.getElementById('timeChart'), timeChartConfig);
const departmentChart = new Chart(document.getElementById('departmentChart'), departmentChartConfig);
</script>

<?php
// Include the footer
include './includes/footer.php';
?>
