<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get complaint statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'resolved' => 0,
    'rejected' => 0
];

// Check if database connection is valid
if (!$conn) {
    error_log("Database connection failed on analytics.php");
} else {
    try {
        // Total complaints
        $sql = "SELECT COUNT(*) as count FROM complaints";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total'] = (int)$row['count'];
        } else {
            error_log("Error querying total complaints: " . $conn->error);
        }

        // Complaints by status
        $sql = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats[$row['status']] = (int)$row['count'];
            }
        } else {
            error_log("Error querying complaints by status: " . $conn->error);
        }

        // Get average resolution time
        $avg_resolution_time = 'N/A';
        $sql = "SELECT AVG(TIMESTAMPDIFF(DAY, c.created_at, cu.created_at)) as avg_days
                FROM complaints c
                JOIN complaint_updates cu ON c.id = cu.complaint_id
                WHERE cu.status = 'resolved'
                AND c.status = 'resolved'";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['avg_days'] !== null) {
                $avg_resolution_time = round($row['avg_days'], 1) . ' days';
            }
        } else {
            error_log("Error querying average resolution time: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Exception in analytics.php: " . $e->getMessage());
    }
}

// Get departments for filter
$departments = [];
$sql = "SELECT * FROM departments ORDER BY name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h4>
                    <div>
                        <a href="dashboard.php" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-tachometer-alt me-2"></i>Main Dashboard
                        </a>
                        <a href="../index.php" class="btn btn-light btn-sm">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Complaints</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalComplaints"><?php echo $stats['total']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolved Complaints</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolvedComplaints"><?php echo $stats['resolved']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Complaints</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingComplaints"><?php echo $stats['pending']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg. Resolution Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avgResolutionTime">
                                <?php echo $avg_resolution_time; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Complaints Trend (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Complaints by Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 300px;">
                        <canvas id="complaintsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Complaints by Department</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 300px;">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Complaints by Priority</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 300px;">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Department Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="performanceTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Total Complaints</th>
                                    <th>Resolved</th>
                                    <th>Pending</th>
                                    <th>Resolution Rate</th>
                                    <th>Avg. Resolution Time</th>
                                </tr>
                            </thead>
                            <tbody id="performanceTableBody">
                                <!-- Will be populated by JavaScript -->
                                <tr>
                                    <td colspan="6" class="text-center">Loading department performance data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Include Dashboard JS -->
<script src="../assets/js/dashboard.js"></script>

<script>
// Additional JavaScript for department performance table
document.addEventListener('DOMContentLoaded', function() {
    // Fetch department performance data
    fetch('../api/department_performance.php')
        .then(response => response.json())
        .then(data => {
            populatePerformanceTable(data);
        })
        .catch(error => {
            console.error('Error fetching department performance data:', error);
            document.getElementById('performanceTableBody').innerHTML = 
                '<tr><td colspan="6" class="text-center text-danger">Error loading department performance data</td></tr>';
        });
});

function populatePerformanceTable(data) {
    const tableBody = document.getElementById('performanceTableBody');
    
    if (data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No department data available</td></tr>';
        return;
    }
    
    let html = '';
    
    data.forEach(dept => {
        const resolutionRate = dept.total > 0 ? Math.round((dept.resolved / dept.total) * 100) : 0;
        
        html += `<tr>
            <td>${dept.name}</td>
            <td>${dept.total}</td>
            <td>${dept.resolved}</td>
            <td>${dept.pending}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="me-2">${resolutionRate}%</span>
                    <div class="progress" style="height: 10px; width: 100px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${resolutionRate}%" 
                            aria-valuenow="${resolutionRate}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </td>
            <td>${dept.avg_resolution_time || 'N/A'}</td>
        </tr>`;
    });
    
    tableBody.innerHTML = html;
}
</script>

<?php require_once '../includes/footer.php'; ?>