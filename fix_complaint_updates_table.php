<?php
require_once 'config/database.php';

if ($conn) {
    echo "Database connection successful\n";
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Modify the status column to match schema.sql
        $sql = "ALTER TABLE complaint_updates MODIFY COLUMN status VARCHAR(50) NOT NULL";
        if ($conn->query($sql)) {
            echo "Successfully updated status column to VARCHAR(50)\n";
        } else {
            throw new Exception("Error updating status column: " . $conn->error);
        }
        
        // Modify the description column to allow NULL values
        $sql = "ALTER TABLE complaint_updates MODIFY COLUMN description TEXT NULL";
        if ($conn->query($sql)) {
            echo "Successfully updated description column to allow NULL values\n";
        } else {
            throw new Exception("Error updating description column: " . $conn->error);
        }
        
        // Commit the changes
        $conn->commit();
        echo "Database structure updated successfully!\n";
        
        // Show the updated table structure
        $result = $conn->query("DESCRIBE complaint_updates");
        echo "\nUpdated complaint_updates table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")" . 
                 ($row['Null'] === 'NO' ? ' NOT NULL' : '') . 
                 ($row['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . 
                 ($row['Default'] ? " DEFAULT '" . $row['Default'] . "'" : '') . 
                 ($row['Extra'] ? " " . $row['Extra'] : '') . "\n";
        }
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed\n";
}
?>