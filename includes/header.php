<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session data
error_log("Header.php - Session data: " . print_r($_SESSION, true));

require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Civic Complaints System</title>
    <meta name="description" content="A comprehensive system for citizens to submit and track civic complaints for proactive maintenance">
    <meta name="theme-color" content="#0d6efd">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $basePath; ?>assets/images/favicon.png">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php
    // Determine if we're in the admin section
    $isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
    $basePath = $isAdmin ? '../' : '';
    ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/mobile.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/dark-mode.css">
    <!-- Open Graph tags for social sharing -->
    <meta property="og:title" content="Civic Complaints System">
    <meta property="og:description" content="Report and track civic issues in your community">
    <meta property="og:image" content="assets/images/og-image.jpg">
    <meta property="og:url" content="https://yourciviccomplaints.com">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $basePath; ?>index.php">
                <i class="fas fa-building me-2"></i>Civic Complaints
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>index.php">Home</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>submit_complaint.php">Submit Complaint</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>my_complaints.php">My Complaints</a>
                        </li>
                        <?php if(strtolower($_SESSION['role']) === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $isAdmin ? 'dashboard.php' : 'admin/dashboard.php'; ?>">Admin Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>notifications.php">
                                    Notifications
                                    <?php
                                    // Count unread notifications
                                    $notification_count = 0;
                                    if(isset($_SESSION['user_id'])) {
                                        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'pending'";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $_SESSION['user_id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $row = $result->fetch_assoc();
                                        $notification_count = $row['count'];
                                    }
                                    if($notification_count > 0): ?>
                                        <span class="badge bg-danger"><?php echo $notification_count; ?></span>
                                    <?php endif; ?>
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4"> 