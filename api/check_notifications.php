<?php
require_once '../includes/header.php';
require_once '../includes/notifications.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get unread notification count
$count = $notificationSystem->getUnreadCount($_SESSION['user_id']);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?> 