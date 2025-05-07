<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config/database.php';

echo "<h1>Complaints Table Check</h1>";

// Check connection
if (!$conn) {
    echo "<p>Database connection failed: " . mysqli_connect_error() . "</p>";
} else {
    echo "<p>Database connection successful: " . $conn->host_info . "</p>";
    
    // Check if complaints table exists
    $result = $conn->query("SHOW TABLES LIKE 'complaints'");
    if ($result->num_rows > 0) {
        echo "<p>Complaints table exists</p>";
        
        // Show table structure
        echo "<h2>Table Structure</h2>";
        $structure_result = $conn->query("DESCRIBE complaints");
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
        
        // Show all complaints
        echo "<h2>All Complaints</h2>";
        $complaints_result = $conn->query("SELECT * FROM complaints");
        if ($complaints_result) {
            if ($complaints_result->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr>";
                $fields = $complaints_result->fetch_fields();
                foreach ($fields as $field) {
                    echo "<th>" . $field->name . "</th>";
                }
                echo "</tr>";
                
                $complaints_result->data_seek(0); // Reset result pointer
                while ($row = $complaints_result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $key => $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No complaints found in the database</p>";
            }
        } else {
            echo "<p>Error querying complaints: " . $conn->error . "</p>";
        }
        
        // Show status distribution
        echo "<h2>Complaints by Status</h2>";
        $status_result = $conn->query("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
        if ($status_result) {
            if ($status_result->num_rows > 0) {
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
                echo "<p>No status distribution found</p>";
            }
        } else {
            echo "<p>Error querying status distribution: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Complaints table does not exist</p>";
    }
}
?>