<?php
require_once 'includes/header.php';

// Get some statistics for the homepage
$stats = [
    'total' => 0,
    'resolved' => 0,
    'departments' => 0,
    'users' => 0
];

// Check if database connection is valid
if (!$conn) {
    error_log("Database connection failed on index.php");
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
        
        // Resolved complaints
        $sql = "SELECT COUNT(*) as count FROM complaints WHERE status = 'resolved'";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['resolved'] = (int)$row['count'];
        } else {
            error_log("Error querying resolved complaints: " . $conn->error);
        }
        
        // Total departments
        $sql = "SELECT COUNT(*) as count FROM departments";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['departments'] = (int)$row['count'];
        } else {
            error_log("Error querying departments: " . $conn->error);
        }
        
        // Total users
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['users'] = (int)$row['count'];
        } else {
            error_log("Error querying users: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Exception in index.php statistics: " . $e->getMessage());
    }
}

// Get recent complaints for public display
$recent_complaints = [];

try {
    $sql = "SELECT c.id, c.title, c.status, c.created_at, d.name as department_name 
            FROM complaints c 
            JOIN departments d ON c.department_id = d.id 
            ORDER BY c.created_at DESC LIMIT 5";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $recent_complaints[] = $row;
        }
    } else {
        error_log("Error getting recent complaints: " . $conn->error);
    }
} catch (Exception $e) {
    error_log("Exception in recent complaints: " . $e->getMessage());
}
?>

<!-- Hero Section -->
<div class="jumbotron bg-primary text-white text-center py-5 mb-4">
    <div class="container">
        <h1 class="display-4">Civic Complaints System</h1>
        <p class="lead">Report and track civic issues in your community for proactive maintenance</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="mt-4">
                <a href="register_new.php" class="btn btn-light btn-lg me-2">Register</a>
                <a href="login.php" class="btn btn-outline-light btn-lg">Login</a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="submit_complaint.php" class="btn btn-light btn-lg">Submit a Complaint</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics Section -->
<div class="container mb-5">
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-3x mb-3 text-primary"></i>
                    <h3 class="counter"><?php echo $stats['total']; ?></h3>
                    <p class="text-muted">Total Complaints</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h3 class="counter"><?php echo $stats['resolved']; ?></h3>
                    <p class="text-muted">Resolved Issues</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-building fa-3x mb-3 text-info"></i>
                    <h3 class="counter"><?php echo $stats['departments']; ?></h3>
                    <p class="text-muted">Departments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body">
                    <i class="fas fa-users fa-3x mb-3 text-warning"></i>
                    <h3 class="counter"><?php echo $stats['users']; ?></h3>
                    <p class="text-muted">Registered Users</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="container mb-5">
    <h2 class="text-center mb-4">How It Works</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                    <h4>1. Register</h4>
                    <p>Create an account to access all features of the Civic Complaints System.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-edit fa-2x"></i>
                    </div>
                    <h4>2. Submit Complaint</h4>
                    <p>Provide details about the issue including location and images.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <h4>3. Track Progress</h4>
                    <p>Get real-time updates on the status of your submitted complaints.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Complaints Section -->
<div class="container mb-5">
    <h2 class="text-center mb-4">Recent Complaints</h2>
    <?php if (empty($recent_complaints)): ?>
        <div class="alert alert-info text-center">
            No complaints have been submitted yet.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($recent_complaints as $complaint): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><?php echo htmlspecialchars($complaint['title']); ?></h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Department:</strong> <?php echo htmlspecialchars($complaint['department_name']); ?></p>
                            <p><strong>Status:</strong> 
                                <span class="complaint-status status-<?php echo str_replace('_', '-', $complaint['status']); ?>">
                                    <?php echo str_replace('_', ' ', ucfirst($complaint['status'])); ?>
                                </span>
                            </p>
                            <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Call to Action -->
<div class="bg-light py-5 mb-5">
    <div class="container text-center">
        <h2>Ready to report an issue?</h2>
        <p class="lead mb-4">Help improve your community by reporting civic issues</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register_new.php" class="btn btn-primary btn-lg">Get Started</a>
        <?php else: ?>
            <a href="submit_complaint.php" class="btn btn-primary btn-lg">Submit a Complaint</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>