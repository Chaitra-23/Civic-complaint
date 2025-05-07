<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config/database.php';

echo "<h1>Database Status Check</h1>";

// Check connection
if (!$conn) {
    echo "<p>Database connection failed: " . mysqli_connect_error() . "</p>";
} else {
    echo "<p>Database connection successful: " . $conn->host_info . "</p>";
    
    // Check tables
    $tables = ['users', 'departments', 'complaints'];
    foreach ($tables as $table) {
        echo "<h2>Table: $table</h2>";
        
        // Check if table exists
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p>Table exists</p>";
            
            // Get row count
            $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
            if ($count_result) {
                $row = $count_result->fetch_assoc();
                echo "<p>Total rows: " . $row['count'] . "</p>";
            } else {
                echo "<p>Error getting row count: " . $conn->error . "</p>";
            }
            
            // Show table structure
            echo "<h3>Table Structure</h3>";
            $structure_result = $conn->query("DESCRIBE $table");
            if ($structure_result) {
                echo "<table border='1'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                while ($row = $structure_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Field'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Null'] . "</td>";
                    echo "<td>" . $row['Key'] . "</td>";
                    echo "<td>" . $row['Default'] . "</td>";
                    echo "<td>" . $row['Extra'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Error getting table structure: " . $conn->error . "</p>";
            }
            
            // If it's the complaints table, show status distribution
            if ($table === 'complaints') {
                echo "<h3>Complaints by Status</h3>";
                $status_result = $conn->query("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
                if ($status_result) {
                    echo "<table border='1'>";
                    echo "<tr><th>Status</th><th>Count</th></tr>";
                    while ($row = $status_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['count'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>Error getting status distribution: " . $conn->error . "</p>";
                }
            }
        } else {
            echo "<p>Table does not exist</p>";
        }
    }
}
?>