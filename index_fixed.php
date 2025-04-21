<?php
// Start session
session_start();

// Include the fixed database connection
require_once 'config/database_fixed.php';

// Get some statistics for the homepage
$stats = [
    'total' => 0,
    'resolved' => 0,
    'departments' => 0,
    'users' => 0
];

// Debug information
$debug_info = [];
$debug_info[] = "Database connection status: " . ($conn ? "Connected" : "Not connected");

// Check if database connection is valid
if (!$conn) {
    error_log("Database connection failed on index_fixed.php");
    $debug_info[] = "Database connection failed";
} else {
    try {
        // Total complaints - using direct query for simplicity
        $debug_info[] = "Attempting to get total complaints";
        $result = $conn->query("SELECT COUNT(*) as count FROM complaints");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['total'] = (int)$row['count'];
            $debug_info[] = "Total complaints: " . $stats['total'];
        } else {
            $debug_info[] = "Error querying total complaints: " . $conn->error;
        }

        // Resolved complaints
        $debug_info[] = "Attempting to get resolved complaints";
        $result = $conn->query("SELECT COUNT(*) as count FROM complaints WHERE status = 'resolved'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['resolved'] = (int)$row['count'];
            $debug_info[] = "Resolved complaints: " . $stats['resolved'];
        } else {
            $debug_info[] = "Error querying resolved complaints: " . $conn->error;
        }

        // Total departments
        $debug_info[] = "Attempting to get departments count";
        $result = $conn->query("SELECT COUNT(*) as count FROM departments");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['departments'] = (int)$row['count'];
            $debug_info[] = "Departments: " . $stats['departments'];
        } else {
            $debug_info[] = "Error querying departments: " . $conn->error;
        }

        // Total users
        $debug_info[] = "Attempting to get users count";
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['users'] = (int)$row['count'];
            $debug_info[] = "Users: " . $stats['users'];
        } else {
            $debug_info[] = "Error querying users: " . $conn->error;
        }
    } catch (Exception $e) {
        $debug_info[] = "Exception: " . $e->getMessage();
        error_log("Exception in index_fixed.php statistics: " . $e->getMessage());
    }
}

// Write debug info to log file
$log_file = __DIR__ . '/debug_log_fixed.txt';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . implode("\n", $debug_info) . "\n\n", FILE_APPEND);

// Output the statistics as JSON for debugging
header('Content-Type: application/json');
echo json_encode([
    'stats' => $stats,
    'debug_info' => $debug_info,
    'connection_status' => $conn ? 'connected' : 'not connected'
]);
?>