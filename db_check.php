<?php
// Include database connection
require_once 'config/database.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>Database Check</h1>";

// Check if tables exist
$tables = ['complaints', 'departments', 'users'];
foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($sql);
    
    echo "<h2>Table: $table</h2>";
    if ($result && $result->num_rows > 0) {
        echo "Table exists<br>";
        
        // Count records
        $count_sql = "SELECT COUNT(*) as count FROM $table";
        $count_result = $conn->query($count_sql);
        if ($count_result) {
            $row = $count_result->fetch_assoc();
            echo "Record count: " . $row['count'] . "<br>";
        } else {
            echo "Error counting records: " . $conn->error . "<br>";
        }
        
        // Show table structure
        $struct_sql = "DESCRIBE $table";
        $struct_result = $conn->query($struct_sql);
        if ($struct_result) {
            echo "<h3>Table Structure:</h3>";
            echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = $struct_result->fetch_assoc()) {
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
            echo "Error getting table structure: " . $conn->error . "<br>";
        }
    } else {
        echo "Table does not exist<br>";
    }
    echo "<hr>";
}

// Check if the database connection is working
echo "<h2>Database Connection Test</h2>";
if ($conn) {
    echo "Database connection is working properly.<br>";
    echo "Server info: " . $conn->server_info . "<br>";
    echo "Host info: " . $conn->host_info . "<br>";
} else {
    echo "Database connection failed.<br>";
}
?>