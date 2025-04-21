<?php
require_once '../includes/header.php';
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get complaint ID from request
$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($complaint_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid complaint ID']);
    exit();
}

// Get complaint status
$sql = "SELECT status FROM complaints WHERE id = ? AND (user_id = ? OR ? IN (SELECT id FROM users WHERE role = 'admin'))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $complaint_id, $_SESSION['user_id'], $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Complaint not found']);
    exit();
}

$complaint = $result->fetch_assoc();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['status' => $complaint['status']]);
?> 