<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$errors = [];
$success = false;

// Get user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Check if email is already used by another user
    if ($email !== $user['email']) {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email is already in use by another account";
        }
    }
    
    // Check if password change is requested
    $update_password = false;
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        }
        
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
        
        $update_password = true;
    }
    
    // Update user profile if no errors
    if (empty($errors)) {
        if ($update_password) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update user with new password
            $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $full_name, $email, $phone, $address, $hashed_password, $_SESSION['user_id']);
        } else {
            // Update user without changing password
            $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            $success = true;
            
            // Update session variables
            $_SESSION['email'] = $email;
            
            // Refresh user data
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $errors[] = "Error updating profile. Please try again.";
        }
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle">
                            <span class="avatar-text"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                        </div>
                        <h4 class="mt-3"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                        <p class="text-muted"><?php echo $user['role'] === 'admin' ? 'Administrator' : 'User'; ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <p><strong><i class="fas fa-user me-2"></i>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                        <p><strong><i class="fas fa-map-marker-alt me-2"></i>Address:</strong> <?php echo !empty($user['address']) ? htmlspecialchars($user['address']) : 'Not provided'; ?></p>
                        <p><strong><i class="fas fa-calendar-alt me-2"></i>Joined:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Activity Summary</h5>
                </div>
                <div class="card-body">
                    <?php
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
                        error_log("Database connection failed on profile.php");
                    } else {
                        try {
                            // First get total count directly
                            $sql = "SELECT COUNT(*) as count FROM complaints WHERE user_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result) {
                                $row = $result->fetch_assoc();
                                $stats['total'] = (int)$row['count'];
                            } else {
                                error_log("Error querying total complaints for user: " . $conn->error);
                            }
                            
                            // Then get counts by status
                            $sql = "SELECT status, COUNT(*) as count FROM complaints WHERE user_id = ? GROUP BY status";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    $stats[$row['status']] = (int)$row['count'];
                                }
                            } else {
                                error_log("Error querying complaints by status for user: " . $conn->error);
                            }
                        } catch (Exception $e) {
                            error_log("Exception in profile.php statistics: " . $e->getMessage());
                        }
                    }
                    ?>
                    
                    <div class="mb-3">
                        <p><strong>Total Complaints:</strong> <?php echo $stats['total']; ?></p>
                        <?php if ($stats['total'] > 0): ?>
                            <div class="progress mb-2" style="height: 20px;">
                                <?php 
                                    $pending_percent = ($stats['pending'] / $stats['total']) * 100;
                                    $in_progress_percent = ($stats['in_progress'] / $stats['total']) * 100;
                                    $resolved_percent = ($stats['resolved'] / $stats['total']) * 100;
                                    $rejected_percent = ($stats['rejected'] / $stats['total']) * 100;
                                ?>
                                <?php if ($stats['pending'] > 0): ?>
                                    <div class="progress-bar bg-warning" style="width: <?php echo $pending_percent; ?>%">
                                        Pending (<?php echo $stats['pending']; ?>)
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($stats['in_progress'] > 0): ?>
                                    <div class="progress-bar bg-info" style="width: <?php echo $in_progress_percent; ?>%">
                                        In Progress (<?php echo $stats['in_progress']; ?>)
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($stats['resolved'] > 0): ?>
                                    <div class="progress-bar bg-success" style="width: <?php echo $resolved_percent; ?>%">
                                        Resolved (<?php echo $stats['resolved']; ?>)
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($stats['rejected'] > 0): ?>
                                    <div class="progress-bar bg-danger" style="width: <?php echo $rejected_percent; ?>%">
                                        Rejected (<?php echo $stats['rejected']; ?>)
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                You haven't submitted any complaints yet.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid">
                        <a href="my_complaints.php" class="btn btn-primary">
                            <i class="fas fa-clipboard-list me-2"></i>View My Complaints
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Profile</h5>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Profile updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="profile.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address (Optional)</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">Change Password</h5>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            <small class="text-muted">Leave blank if you don't want to change your password</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background-color: #3498db;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
}

.avatar-text {
    font-size: 48px;
    color: white;
    font-weight: bold;
}
</style>

<?php require_once 'includes/footer.php'; ?>