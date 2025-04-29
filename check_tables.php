<?php
require_once 'config/database.php';

if ($conn) {
    echo "Database connection successful\n";
    
    // Check if complaint_updates table exists
    $result = $conn->query("SHOW TABLES LIKE 'complaint_updates'");
    echo "complaint_updates table exists: " . ($result->num_rows > 0 ? 'Yes' : 'No') . "\n";
    
    // List all tables
    echo "All tables in database:\n";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>