<?php
require_once 'config/database.php';

// Create database tables
function createTables($conn) {
    // Read schema.sql file
    $sql = file_get_contents('database/schema.sql');
    
    // Execute SQL statements
    if ($conn->multi_query($sql)) {
        do {
            // Store first result set
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
    
    if ($conn->errno) {
        echo "Error creating tables: " . $conn->error . "<br>";
        return false;
    }
    
    echo "Database tables created successfully.<br>";
    return true;
}

// Insert sample data
function insertSampleData($conn) {
    // Insert admin user
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, full_name, role) 
            VALUES ('admin', 'admin@example.com', ?, 'System Administrator', 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_password);
    $stmt->execute();
    
    // Insert regular user
    $user_password = password_hash('user123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, full_name, phone, address) 
            VALUES ('user', 'user@example.com', ?, 'John Doe', '555-123-4567', '123 Main St, Anytown, USA')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_password);
    $stmt->execute();
    
    // Insert departments
    $departments = [
        ['Water Supply', 'Handles issues related to water supply and distribution'],
        ['Roads and Infrastructure', 'Manages road maintenance, street lights, and public infrastructure'],
        ['Sanitation', 'Responsible for waste management and public cleanliness'],
        ['Electricity', 'Handles power supply issues and electrical infrastructure'],
        ['Parks and Recreation', 'Maintains public parks, gardens, and recreational facilities']
    ];
    
    $sql = "INSERT INTO departments (name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($departments as $dept) {
        $stmt->bind_param("ss", $dept[0], $dept[1]);
        $stmt->execute();
    }
    
    // Insert sample complaints
    $complaints = [
        [
            'user_id' => 2,
            'department_id' => 1,
            'title' => 'Water leakage on Main Street',
            'description' => 'There is a major water leakage on Main Street near the intersection with Oak Avenue. Water has been flowing for the past 2 days and is causing traffic issues.',
            'location' => 'Main Street and Oak Avenue intersection',
            'status' => 'pending',
            'priority' => 'high'
        ],
        [
            'user_id' => 2,
            'department_id' => 2,
            'title' => 'Pothole on Elm Street',
            'description' => 'There is a large pothole on Elm Street that is causing damage to vehicles. It is approximately 2 feet wide and 6 inches deep.',
            'location' => '456 Elm Street, near the grocery store',
            'status' => 'in_progress',
            'priority' => 'medium'
        ],
        [
            'user_id' => 2,
            'department_id' => 3,
            'title' => 'Garbage not collected',
            'description' => 'The garbage in our neighborhood has not been collected for the past week. This is causing sanitation issues and bad odor in the area.',
            'location' => 'Pine Avenue, between 5th and 6th Street',
            'status' => 'resolved',
            'priority' => 'medium'
        ]
    ];
    
    $sql = "INSERT INTO complaints (user_id, department_id, title, description, location, status, priority) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($complaints as $complaint) {
        $stmt->bind_param(
            "iisssss",
            $complaint['user_id'],
            $complaint['department_id'],
            $complaint['title'],
            $complaint['description'],
            $complaint['location'],
            $complaint['status'],
            $complaint['priority']
        );
        $stmt->execute();
    }
    
    // Insert sample complaint updates
    $updates = [
        [
            'complaint_id' => 2,
            'status' => 'in_progress',
            'description' => 'Road maintenance team has been dispatched to assess the pothole damage.',
            'created_by' => 1
        ],
        [
            'complaint_id' => 3,
            'status' => 'in_progress',
            'description' => 'Sanitation team has been notified of the missed collection.',
            'created_by' => 1
        ],
        [
            'complaint_id' => 3,
            'status' => 'resolved',
            'description' => 'Garbage has been collected and the area has been cleaned.',
            'created_by' => 1
        ]
    ];
    
    $sql = "INSERT INTO complaint_updates (complaint_id, status, description, created_by) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($updates as $update) {
        $stmt->bind_param(
            "issi",
            $update['complaint_id'],
            $update['status'],
            $update['description'],
            $update['created_by']
        );
        $stmt->execute();
    }
    
    // Insert sample notifications
    $notifications = [
        [
            'user_id' => 2,
            'complaint_id' => 2,
            'message' => 'Your complaint about the pothole on Elm Street has been updated to "In Progress".',
            'type' => 'email'
        ],
        [
            'user_id' => 2,
            'complaint_id' => 3,
            'message' => 'Your complaint about garbage collection has been updated to "In Progress".',
            'type' => 'email'
        ],
        [
            'user_id' => 2,
            'complaint_id' => 3,
            'message' => 'Your complaint about garbage collection has been resolved. Thank you for your patience.',
            'type' => 'email'
        ]
    ];
    
    $sql = "INSERT INTO notifications (user_id, complaint_id, message, type) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    foreach ($notifications as $notification) {
        $stmt->bind_param(
            "iiss",
            $notification['user_id'],
            $notification['complaint_id'],
            $notification['message'],
            $notification['type']
        );
        $stmt->execute();
    }
    
    echo "Sample data inserted successfully.<br>";
    return true;
}

// Main setup function
function setupDatabase($conn) {
    echo "<h2>Setting up Civic Complaints System Database</h2>";
    
    // Create tables
    if (!createTables($conn)) {
        return false;
    }
    
    // Insert sample data
    if (!insertSampleData($conn)) {
        return false;
    }
    
    echo "<h3>Setup completed successfully!</h3>";
    echo "<p>You can now <a href='login.php'>login</a> with the following credentials:</p>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> Username: admin, Password: admin123</li>";
    echo "<li><strong>User:</strong> Username: user, Password: user123</li>";
    echo "</ul>";
    
    return true;
}

// Run setup
setupDatabase($conn);
?>