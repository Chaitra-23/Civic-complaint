<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Check if complaint ID is provided in URL
$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $complaint_id = isset($_POST['complaint_id']) ? (int)$_POST['complaint_id'] : 0;
    $status = $_POST['status'];
    $description = trim($_POST['description']);
    
    // Validate input
    $errors = [];
    if (empty($status)) $errors[] = "Status is required";
    if (empty($description)) $errors[] = "Description is required";
    
    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update complaint status
            $sql = "UPDATE complaints SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $status, $complaint_id);
            $stmt->execute();
            
            // Add status update record
            $sql = "INSERT INTO complaint_updates (complaint_id, status, description, created_by) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issi", $complaint_id, $status, $description, $_SESSION['user_id']);
            $stmt->execute();
            
            // Get user ID for notification
            $sql = "SELECT user_id FROM complaints WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $complaint_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
            
            // Add notification
            $message = "Your complaint status has been updated to: " . ucfirst(str_replace('_', ' ', $status));
            $sql = "INSERT INTO notifications (user_id, complaint_id, message, type) VALUES (?, ?, ?, 'email')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $user_id, $complaint_id, $message);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['message'] = "Complaint status updated successfully!";
            $_SESSION['message_type'] = "success";
            header("Location: dashboard.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $errors[] = "Error updating status: " . $e->getMessage();
        }
    }
}

// Get complaint details if ID is provided
if ($complaint_id > 0) {
    $sql = "SELECT c.*, d.name as department_name, u.username, u.email 
            FROM complaints c 
            JOIN departments d ON c.department_id = d.id 
            JOIN users u ON c.user_id = u.id 
            WHERE c.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Location: dashboard.php');
        exit();
    }
    
    $complaint = $result->fetch_assoc();
} else {
    header('Location: dashboard.php');
    exit();
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Update Complaint Status</h4>
                    <a href="dashboard.php" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <h5>Complaint Information</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 150px;">ID</th>
                                    <td><?php echo $complaint['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Title</th>
                                    <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                                </tr>
                                <tr>
                                    <th>Submitted By</th>
                                    <td><?php echo htmlspecialchars($complaint['username']); ?> (<?php echo htmlspecialchars($complaint['email']); ?>)</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td><?php echo htmlspecialchars($complaint['department_name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Current Status</th>
                                    <td>
                                        <span class="complaint-status status-<?php echo str_replace('_', '-', $complaint['status']); ?>">
                                            <?php echo str_replace('_', ' ', ucfirst($complaint['status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Priority</th>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $complaint['priority'] === 'high' ? 'danger' : 
                                                ($complaint['priority'] === 'medium' ? 'warning' : 'info'); 
                                        ?>">
                                            <?php echo ucfirst($complaint['priority']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Submitted On</th>
                                    <td><?php echo date('F d, Y \a\t h:i A', strtotime($complaint['created_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <form action="update_status.php" method="post">
                        <input type="hidden" name="complaint_id" value="<?php echo $complaint_id; ?>">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">New Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending" <?php echo $complaint['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $complaint['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo $complaint['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option value="rejected" <?php echo $complaint['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Update Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            <small class="text-muted">Provide details about this status update. This will be visible to the user.</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>