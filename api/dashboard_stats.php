<?php
/**
 * API endpoint for dashboard statistics
 * Returns JSON data for dashboard charts
 */

// Include database connection
require_once '../config/database.php';

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Initialize response array
$response = [
    'statusData' => [],
    'departmentData' => [],
    'priorityData' => [],
    'monthlyData' => [],
    'stats' => []
];

// Get complaints by status
try {
    $sql = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
    $result = $conn->query($sql);
    $statusData = [
        'pending' => 0,
        'in_progress' => 0,
        'resolved' => 0,
        'rejected' => 0
    ];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $statusData[$row['status']] = (int)$row['count'];
        }
    } else {
        error_log("Error querying complaints by status in API: " . $conn->error);
    }
    $response['statusData'] = $statusData;
} catch (Exception $e) {
    error_log("Exception in dashboard_stats.php (status data): " . $e->getMessage());
    $response['statusData'] = [
        'pending' => 0,
        'in_progress' => 0,
        'resolved' => 0,
        'rejected' => 0
    ];
}

// Get complaints by department
$sql = "SELECT d.name, COUNT(c.id) as count 
        FROM departments d 
        LEFT JOIN complaints c ON d.id = c.department_id 
        GROUP BY d.id 
        ORDER BY count DESC";
$result = $conn->query($sql);
$departmentData = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departmentData[] = [
            'name' => $row['name'],
            'count' => (int)$row['count']
        ];
    }
}
$response['departmentData'] = $departmentData;

// Get complaints by priority
$sql = "SELECT priority, COUNT(*) as count FROM complaints GROUP BY priority";
$result = $conn->query($sql);
$priorityData = [
    'high' => 0,
    'medium' => 0,
    'low' => 0
];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $priorityData[$row['priority']] = (int)$row['count'];
    }
}
$response['priorityData'] = $priorityData;

// Get monthly complaints for the last 6 months
$sql = "SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as count 
        FROM complaints 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
        GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
        ORDER BY created_at";
$result = $conn->query($sql);
$monthlyData = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $monthlyData[] = [
            'month' => $row['month'],
            'count' => (int)$row['count']
        ];
    }
}
$response['monthlyData'] = $monthlyData;

// Get general statistics
try {
    $stats = [
        'total' => 0,
        'resolved' => 0,
        'pending' => 0,
        'avg_resolution_time' => 'N/A'
    ];

    // Total complaints
    $sql = "SELECT COUNT(*) as count FROM complaints";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = (int)$row['count'];
    } else {
        error_log("Error querying total complaints in API: " . $conn->error);
    }

    // Resolved complaints
    $sql = "SELECT COUNT(*) as count FROM complaints WHERE status = 'resolved'";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['resolved'] = (int)$row['count'];
    } else {
        error_log("Error querying resolved complaints in API: " . $conn->error);
    }

    // Pending complaints
    $sql = "SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['pending'] = (int)$row['count'];
    } else {
        error_log("Error querying pending complaints in API: " . $conn->error);
    }

    // Average resolution time (for resolved complaints)
    $sql = "SELECT AVG(TIMESTAMPDIFF(DAY, c.created_at, cu.created_at)) as avg_days
            FROM complaints c
            JOIN complaint_updates cu ON c.id = cu.complaint_id
            WHERE cu.status = 'resolved'
            AND c.status = 'resolved'";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['avg_days'] !== null) {
            $stats['avg_resolution_time'] = round($row['avg_days'], 1) . ' days';
        }
    } else {
        error_log("Error querying avg resolution time in API: " . $conn->error);
    }

    $response['stats'] = $stats;
} catch (Exception $e) {
    error_log("Exception in dashboard_stats.php (general stats): " . $e->getMessage());
    $response['stats'] = [
        'total' => 0,
        'resolved' => 0,
        'pending' => 0,
        'avg_resolution_time' => 'N/A'
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>