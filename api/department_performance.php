<?php
/**
 * API endpoint for department performance data
 * Returns JSON data for department performance metrics
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

// Get department performance data
$sql = "SELECT 
            d.id,
            d.name,
            COUNT(c.id) as total,
            SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) as resolved,
            SUM(CASE WHEN c.status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN c.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN c.status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM 
            departments d
        LEFT JOIN 
            complaints c ON d.id = c.department_id
        GROUP BY 
            d.id
        ORDER BY 
            total DESC";

$result = $conn->query($sql);
$departments = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Calculate average resolution time for this department
        $dept_id = $row['id'];
        $avg_time_sql = "SELECT 
                            AVG(TIMESTAMPDIFF(DAY, c.created_at, cu.created_at)) as avg_days
                        FROM 
                            complaints c
                        JOIN 
                            complaint_updates cu ON c.id = cu.complaint_id
                        WHERE 
                            c.department_id = $dept_id
                        AND 
                            cu.status = 'resolved'
                        AND 
                            c.status = 'resolved'";
        
        $avg_result = $conn->query($avg_time_sql);
        $avg_row = $avg_result->fetch_assoc();
        
        $avg_resolution_time = null;
        if ($avg_row && $avg_row['avg_days'] !== null) {
            $avg_resolution_time = round($avg_row['avg_days'], 1) . ' days';
        }
        
        $departments[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'total' => (int)$row['total'],
            'resolved' => (int)$row['resolved'],
            'pending' => (int)$row['pending'],
            'in_progress' => (int)$row['in_progress'],
            'rejected' => (int)$row['rejected'],
            'avg_resolution_time' => $avg_resolution_time
        ];
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($departments);
?>