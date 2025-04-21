<?php
require_once 'includes/header.php';
require_once 'includes/notifications.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Mark notification as read if requested
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notificationSystem->markAsRead($_GET['mark_read']);
    header('Location: notifications.php');
    exit();
}

// Get user's notifications
$notifications = $notificationSystem->getUserNotifications($_SESSION['user_id']);
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="alert alert-info">
                            You have no notifications.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            <?php if ($notification['status'] === 'sent'): ?>
                                                <span class="badge bg-danger me-2">New</span>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($notification['complaint_title']); ?>
                                        </h5>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                    <div class="mt-2">
                                        <a href="view_complaint.php?id=<?php echo $notification['complaint_id']; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>View Complaint
                                        </a>
                                        <?php if ($notification['status'] === 'sent'): ?>
                                            <a href="notifications.php?mark_read=<?php echo $notification['id']; ?>" 
                                               class="btn btn-sm btn-secondary">
                                                <i class="fas fa-check me-1"></i>Mark as Read
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 