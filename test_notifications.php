<?php
require_once 'config/database.php';

echo "Testing notifications table:\n";

// Check if notifications table exists
$result = $conn->query("SHOW TABLES LIKE 'notifications'");
if ($result->num_rows == 0) {
    echo "Notifications table does not exist. Creating it now...\n";
    
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
} else {
    echo "Notifications table exists.\n";
    
    // Show table structure
    $result = $conn->query("DESCRIBE notifications");
    echo "Table structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")" . 
             ($row['Null'] === 'NO' ? ' NOT NULL' : '') . 
             ($row['Key'] === 'PRI' ? ' PRIMARY KEY' : '') . 
             ($row['Default'] ? " DEFAULT '" . $row['Default'] . "'" : '') . 
             ($row['Extra'] ? " " . $row['Extra'] : '') . "\n";
    }
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM notifications");
    $row = $result->fetch_assoc();
    echo "Number of notifications: " . $row['count'] . "\n";
}

// Test inserting a notification
echo "\nTesting notification insertion:\n";
$user_id = 1; // Assuming user ID 1 exists
$complaint_id = 1; // Assuming complaint ID 1 exists
$message = "Test notification";

$sql = "INSERT INTO notifications (user_id, complaint_id, message, type) VALUES (?, ?, ?, 'email')";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Error preparing statement: " . $conn->error . "\n";
} else {
    $stmt->bind_param("iis", $user_id, $complaint_id, $message);
    
    if ($stmt->execute()) {
        echo "Test notification inserted successfully.\n";
    } else {
        echo "Error executing statement: " . $stmt->error . "\n";
    }
}
?>