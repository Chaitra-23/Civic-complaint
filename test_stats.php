<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config/database.php';

echo "<h1>Testing Stats Calculation</h1>";

// Check connection
if (!$conn) {
    echo "<p>Database connection failed: " . mysqli_connect_error() . "</p>";
} else {
    echo "<p>Database connection successful: " . $conn->host_info . "</p>";
    
    // Initialize stats array
    $stats = [
        'total' => 0,
        'resolved' => 0,
        'departments' => 0,
        'users' => 0,
        'pending' => 0,
        'in_progress' => 0,
        'rejected' => 0
    ];
    
    // Get total complaints
    $sql = "SELECT COUNT(*) as count FROM complaints";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total'] = (int)$row['count'];
        echo "<p>Total complaints: " . $stats['total'] . "</p>";
    } else {
        echo "<p>Error querying total complaints: " . $conn->error . "</p>";
    }
    
    // Get counts by status
    $sql = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
    $result = $conn->query($sql);
    if ($result) {
        echo "<h2>Status Counts:</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Status</th><th>Count</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $status = $row['status'];
            $count = (int)$row['count'];
            
            // Update the stats array
            $stats[$status] = $count;
            
            echo "<tr><td>" . $status . "</td><td>" . $count . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Error querying complaints by status: " . $conn->error . "</p>";
    }
    
    // Get total departments
    $sql = "SELECT COUNT(*) as count FROM departments";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['departments'] = (int)$row['count'];
        echo "<p>Total departments: " . $stats['departments'] . "</p>";
    } else {
        echo "<p>Error querying departments: " . $conn->error . "</p>";
    }
    
    // Get total users
    $sql = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['users'] = (int)$row['count'];
        echo "<p>Total users: " . $stats['users'] . "</p>";
    } else {
        echo "<p>Error querying users: " . $conn->error . "</p>";
    }
    
    // Display final stats array
    echo "<h2>Final Stats Array:</h2>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
}
?>