<?php
// Include database configuration
require_once 'config/database.php';

// Create tables if they don't exist
$tables = [
    // Complaints table
    "CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        location VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        status ENUM('pending', 'in_progress', 'resolved', 'rejected') DEFAULT 'pending',
        user_id INT,
        department_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    // Departments table
    "CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Users table
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'staff', 'citizen') DEFAULT 'citizen',
        department_id INT NULL,
        phone VARCHAR(20) NULL,
        address TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // Notifications table
    "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        complaint_id INT NOT NULL,
        message TEXT NOT NULL,
        type ENUM('email', 'sms', 'system') DEFAULT 'system',
        status ENUM('sent', 'read') DEFAULT 'sent',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

// Execute each table creation query
$success = true;
foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        echo "Error creating table: " . $conn->error . "<br>";
        $success = false;
    }
}

if ($success) {
    echo "Database tables created successfully!<br>";
    
    // Insert some sample data
    $sample_data = [
        // Sample departments
        "INSERT INTO departments (name, description) VALUES 
            ('Public Works', 'Responsible for infrastructure maintenance'),
            ('Sanitation', 'Handles waste management and cleanliness'),
            ('Parks & Recreation', 'Maintains public parks and recreational facilities')",
            
        // Sample users (password is 'password123' hashed)
        "INSERT INTO users (username, password, email, full_name, role) VALUES 
            ('admin', '$2y$10$8zUkFX1tXY5pZuZ8OZ9tT.R8rla3aqRxC1vYgJUEZxHRhPQJRUzWO', 'admin@example.com', 'Admin User', 'admin'),
            ('citizen1', '$2y$10$8zUkFX1tXY5pZuZ8OZ9tT.R8rla3aqRxC1vYgJUEZxHRhPQJRUzWO', 'citizen1@example.com', 'John Citizen', 'citizen')",
            
        // Sample complaints
        "INSERT INTO complaints (title, description, location, category, status, user_id, department_id) VALUES 
            ('Pothole on Main Street', 'Large pothole causing traffic issues', 'Main St & 5th Ave', 'Roads', 'pending', 2, 1),
            ('Overflowing trash bin', 'Trash bin has not been emptied for a week', 'Central Park', 'Sanitation', 'in_progress', 2, 2)"
    ];
    
    // Try to insert sample data, but don't fail if data already exists
    foreach ($sample_data as $sql) {
        $conn->query($sql);
    }
    
    echo "Sample data has been added (if it didn't already exist).<br>";
    
    // Add sample notifications
    $conn->query("INSERT INTO notifications (user_id, complaint_id, message, type, status) VALUES 
        (2, 1, 'Your pothole complaint has been received.', 'system', 'sent'),
        (2, 2, 'Your trash bin complaint is now being processed.', 'system', 'sent')");
}

// Close connection
$conn->close();

echo "<p>You can now <a href='db_test.php'>test the database connection</a> or <a href='notifications.php'>view notifications</a>.</p>";
?>