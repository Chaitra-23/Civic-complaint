<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if complaint ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: my_complaints.php');
    exit();
}

$complaint_id = (int)$_GET['id'];

// Get complaint details
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
    header('Location: my_complaints.php');
    exit();
}

$complaint = $result->fetch_assoc();

// Check if the user is authorized to view this complaint
if ($_SESSION['role'] !== 'admin' && $complaint['user_id'] != $_SESSION['user_id']) {
    header('Location: my_complaints.php');
    exit();
}

// Get complaint images
$images = [];
$sql = "SELECT * FROM complaint_images WHERE complaint_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}

// Get complaint updates
$updates = [];
$sql = "SELECT cu.*, u.username 
        FROM complaint_updates cu 
        JOIN users u ON cu.created_by = u.id 
        WHERE cu.complaint_id = ? 
        ORDER BY cu.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $updates[] = $row;
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Complaint Details</h4>
                    <a href="my_complaints.php" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h3><?php echo htmlspecialchars($complaint['title']); ?></h3>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="complaint-status status-<?php echo str_replace('_', '-', $complaint['status']); ?>">
                                <?php echo str_replace('_', ' ', ucfirst($complaint['status'])); ?>
                            </span>
                            <span class="badge bg-<?php 
                                echo $complaint['priority'] === 'high' ? 'danger' : 
                                    ($complaint['priority'] === 'medium' ? 'warning' : 'info'); 
                            ?>">
                                Priority: <?php echo ucfirst($complaint['priority']); ?>
                            </span>
                            <span class="badge bg-secondary">
                                Department: <?php echo htmlspecialchars($complaint['department_name']); ?>
                            </span>
                        </div>
                        <p class="text-muted">
                            <i class="fas fa-calendar-alt me-2"></i>Submitted on: <?php echo date('F d, Y \a\t h:i A', strtotime($complaint['created_at'])); ?>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Description</h5>
                        <div class="p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Location</h5>
                        <div class="p-3 bg-light rounded">
                            <?php echo htmlspecialchars($complaint['location']); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($images)): ?>
                        <div class="mb-4">
                            <h5>Images</h5>
                            <div class="row">
                                <?php foreach ($images as $image): ?>
                                    <div class="col-md-4 mb-3">
                                        <a href="<?php echo $image['image_path']; ?>" target="_blank">
                                            <img src="<?php echo $image['image_path']; ?>" class="img-fluid complaint-image" alt="Complaint Image">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Status Updates</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($updates)): ?>
                        <div class="alert alert-info">
                            No updates yet. We'll notify you when there's progress on your complaint.
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($updates as $update): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-<?php 
                                        echo $update['status'] === 'resolved' ? 'success' : 
                                            ($update['status'] === 'rejected' ? 'danger' : 
                                                ($update['status'] === 'in_progress' ? 'info' : 'warning')); 
                                    ?>"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1"><?php echo str_replace('_', ' ', ucfirst($update['status'])); ?></h6>
                                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($update['description'])); ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($update['username']); ?> - 
                                            <i class="fas fa-clock me-1"></i><?php echo date('M d, Y h:i A', strtotime($update['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="admin/update_status.php" method="post">
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
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    top: 5px;
}

.timeline-content {
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.timeline-item:last-child .timeline-content {
    border-bottom: none;
}
</style>

<?php require_once 'includes/footer.php'; ?>