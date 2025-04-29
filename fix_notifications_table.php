<?php
require_once 'config/database.php';

echo "Fixing notifications table structure:\n";

// Check if notifications table exists
$result = $conn->query("SHOW TABLES LIKE 'notifications'");
if ($result->num_rows > 0) {
    echo "Notifications table exists. Checking structure...\n";
    
    // Check if complaint_id column exists
    $result = $conn->query("SHOW COLUMNS FROM notifications LIKE 'complaint_id'");
    if ($result->num_rows == 0) {
        echo "complaint_id column does not exist. Adding it...\n";
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Add complaint_id column
            $sql = "ALTER TABLE notifications ADD COLUMN complaint_id INT NOT NULL AFTER user_id";
            if ($conn->query($sql)) {
                echo "complaint_id column added successfully.\n";
            } else {
                throw new Exception("Error adding complaint_id column: " . $conn->error);
            }
            
            // Add foreign key constraint
            $sql = "ALTER TABLE notifications ADD CONSTRAINT fk_notifications_complaint_id FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE";
            if ($conn->query($sql)) {
                echo "Foreign key constraint added successfully.\n";
            } else {
                throw new Exception("Error adding foreign key constraint: " . $conn->error);
            }
            
            // Check if type column exists
            $result = $conn->query("SHOW COLUMNS FROM notifications LIKE 'type'");
            if ($result->num_rows == 0) {
                echo "type column does not exist. Adding it...\n";
                $sql = "ALTER TABLE notifications ADD COLUMN type VARCHAR(20) NOT NULL DEFAULT 'email' AFTER message";
                if ($conn->query($sql)) {
                    echo "type column added successfully.\n";
                } else {
                    throw new Exception("Error adding type column: " . $conn->error);
                }
            } else {
                echo "type column already exists.\n";
            }
            
            // Commit transaction
            $conn->commit();
            echo "Notifications table structure updated successfully!\n";
            
            // Show updated table structure
            $result = $conn->query("DESCRIBE notifications");
            echo "\nUpdated notifications table structure:\n";
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
        echo "complaint_id column already exists.\n";
        
        // Show table structure
        $result = $conn->query("DESCRIBE notifications");
        echo "Current notifications table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")" . 
                 ($row['Null'] === 'NO' ? ' NOT NULL' : '') . 
                 ($row['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . 
                 ($row['Default'] ? " DEFAULT '" . $row['Default'] . "'" : '') . 
                 ($row['Extra'] ? " " . $row['Extra'] : '') . "\n";
        }
    }
} else {
    echo "Notifications table does not exist. Creating it...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        complaint_id INT NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(20) NOT NULL DEFAULT 'email',
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql)) {
        echo "Notifications table created successfully.\n";
    } else {
        echo "Error creating notifications table: " . $conn->error . "\n";
    }
}
?>