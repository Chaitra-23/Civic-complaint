<?php
require_once 'config/database.php';

if ($conn) {
    echo "Database connection successful\n";
    
    // Check complaint_updates table structure
    $result = $conn->query("DESCRIBE complaint_updates");
    echo "complaint_updates table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")" . 
             ($row['Null'] === 'NO' ? ' NOT NULL' : '') . 
             ($row['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . 
             ($row['Default'] ? " DEFAULT '" . $row['Default'] . "'" : '') . 
             ($row['Extra'] ? " " . $row['Extra'] : '') . "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>