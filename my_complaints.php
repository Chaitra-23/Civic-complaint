<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user's complaints
$complaints = [];

// Check if database connection is valid
if (!$conn) {
    error_log("Database connection failed on my_complaints.php");
} else {
    try {
        $sql = "SELECT c.*, d.name as department_name 
                FROM complaints c 
                JOIN departments d ON c.department_id = d.id 
                WHERE c.user_id = ? 
                ORDER BY c.created_at DESC";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $complaints[] = $row;
                }
            } else {
                error_log("Error getting result set: " . $conn->error);
            }
        } else {
            error_log("Error preparing statement: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Exception in my_complaints.php: " . $e->getMessage());
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>My Complaints</h4>
                    <a href="submit_complaint.php" class="btn btn-light">
                        <i class="fas fa-plus-circle me-2"></i>New Complaint
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                            <?php 
                                echo $_SESSION['message']; 
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($complaints)): ?>
                        <div class="alert alert-info">
                            <p class="mb-0">You haven't submitted any complaints yet.</p>
                        </div>
                        <div class="text-center mt-4">
                            <a href="submit_complaint.php" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Submit Your First Complaint
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($complaints as $complaint): ?>
                                        <tr>
                                            <td><?php echo $complaint['id']; ?></td>
                                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                                            <td><?php echo htmlspecialchars($complaint['department_name']); ?></td>
                                            <td>
                                                <span class="complaint-status status-<?php echo str_replace('_', '-', $complaint['status']); ?>">
                                                    <?php echo str_replace('_', ' ', ucfirst($complaint['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $complaint['priority'] === 'high' ? 'danger' : 
                                                        ($complaint['priority'] === 'medium' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($complaint['priority']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                                            <td>
                                                <a href="view_complaint.php?id=<?php echo $complaint['id']; ?>" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
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

<?php require_once 'includes/footer.php'; ?>