<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$department = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$priority = isset($_GET['priority']) ? $_GET['priority'] : 'all';

// Build query
$sql = "SELECT c.*, u.username, u.email, d.name as department_name 
        FROM complaints c 
        JOIN users u ON c.user_id = u.id 
        JOIN departments d ON c.department_id = d.id 
        WHERE 1=1";
$params = [];
$types = "";

if ($status !== 'all') {
    $sql .= " AND c.status = ?";
    $params[] = $status;
    $types .= "s";
}

if ($department > 0) {
    $sql .= " AND c.department_id = ?";
    $params[] = $department;
    $types .= "i";
}

if ($priority !== 'all') {
    $sql .= " AND c.priority = ?";
    $params[] = $priority;
    $types .= "s";
}

$sql .= " ORDER BY c.created_at DESC";

// Get complaints
$complaints = [];
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

// Get departments for filter
$departments = [];
$sql = "SELECT id, name FROM departments ORDER BY name";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}

// Get statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'resolved' => 0,
    'rejected' => 0
];

// Check if database connection is valid
if (!$conn) {
    error_log("Database connection failed on admin/dashboard.php");
} else {
    try {
        // First get total count directly
        $sql = "SELECT COUNT(*) as count FROM complaints";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total'] = (int)$row['count'];
        } else {
            error_log("Error querying total complaints: " . $conn->error);
        }
        
        // Then get counts by status
        $sql = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $stats[$row['status']] = (int)$row['count'];
            }
        } else {
            error_log("Error querying complaints by status: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Exception in admin/dashboard.php: " . $e->getMessage());
    }
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Statistics Cards -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="dashboard-stats bg-light mb-3">
                        <h2><?php echo $stats['total']; ?></h2>
                        <p>Total Complaints</p>
                    </div>
                    <div class="dashboard-stats bg-warning mb-3">
                        <h2><?php echo $stats['pending']; ?></h2>
                        <p>Pending</p>
                    </div>
                    <div class="dashboard-stats bg-info mb-3">
                        <h2><?php echo $stats['in_progress']; ?></h2>
                        <p>In Progress</p>
                    </div>
                    <div class="dashboard-stats bg-success mb-3">
                        <h2><?php echo $stats['resolved']; ?></h2>
                        <p>Resolved</p>
                    </div>
                    <div class="dashboard-stats bg-danger mb-3">
                        <h2><?php echo $stats['rejected']; ?></h2>
                        <p>Rejected</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="analytics.php" class="btn btn-primary">
                            <i class="fas fa-chart-line me-2"></i>View Detailed Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="0" <?php echo $department === 0 ? 'selected' : ''; ?>>All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo $department === $dept['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="all" <?php echo $priority === 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="low" <?php echo $priority === 'low' ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo $priority === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo $priority === 'high' ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Complaints List -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Complaints</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($complaints)): ?>
                        <div class="alert alert-info">
                            No complaints found matching the selected filters.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th>Department</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($complaints as $complaint): ?>
                                        <tr>
                                            <td><?php echo $complaint['id']; ?></td>
                                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                                            <td><?php echo htmlspecialchars($complaint['username']); ?></td>
                                            <td><?php echo htmlspecialchars($complaint['department_name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $complaint['priority'] === 'high' ? 'danger' : 
                                                        ($complaint['priority'] === 'medium' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($complaint['priority']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="complaint-status status-<?php echo $complaint['status']; ?>">
                                                    <?php echo str_replace('_', ' ', ucfirst($complaint['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                                            <td>
                                                <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" 
                                                   class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="update_status.php?id=<?php echo $complaint['id']; ?>" 
                                                   class="btn btn-sm btn-warning" title="Update Status">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 