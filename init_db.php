<?php
// Include database connection
require_once 'config/database.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>Database Initialization</h1>";

// Create tables if they don't exist
$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'departments' => "CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'complaints' => "CREATE TABLE IF NOT EXISTS complaints (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        department_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        location VARCHAR(255),
        status ENUM('pending', 'in_progress', 'resolved', 'rejected') NOT NULL DEFAULT 'pending',
        priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (department_id) REFERENCES departments(id)
    )"
];

foreach ($tables as $table_name => $sql) {
    echo "<h2>Creating table: $table_name</h2>";
    if ($conn->query($sql) === TRUE) {
        echo "Table created successfully or already exists<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Check if we need to insert sample data
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    echo "<h2>Inserting sample data</h2>";
    
    // Insert admin user
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, role) VALUES ('admin', '$admin_password', 'admin@example.com', 'admin')";
    if ($conn->query($sql) === TRUE) {
        echo "Admin user created<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
    
    // Insert regular user
    $user_password = password_hash("user123", PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, role) VALUES ('user', '$user_password', 'user@example.com', 'user')";
    if ($conn->query($sql) === TRUE) {
        echo "Regular user created<br>";
    } else {
        echo "Error creating regular user: " . $conn->error . "<br>";
    }
    
    // Insert departments
    $departments = [
        ['Water Supply', 'Handles issues related to water supply and distribution'],
        ['Roads and Infrastructure', 'Manages road maintenance and infrastructure development'],
        ['Waste Management', 'Responsible for waste collection and disposal'],
        ['Electricity', 'Handles power supply and electrical infrastructure']
    ];
    
    foreach ($departments as $dept) {
        $sql = "INSERT INTO departments (name, description) VALUES ('" . $conn->real_escape_string($dept[0]) . "', '" . $conn->real_escape_string($dept[1]) . "')";
        if ($conn->query($sql) === TRUE) {
            echo "Department '{$dept[0]}' created<br>";
        } else {
            echo "Error creating department: " . $conn->error . "<br>";
        }
    }
    
    // Insert sample complaints
    $complaints = [
        [1, 1, 'Water leakage on Main Street', 'There is a major water leakage on Main Street near the post office', 'Main Street', 'pending', 'high'],
        [1, 2, 'Pothole on Broadway Avenue', 'Large pothole causing traffic issues', 'Broadway Avenue', 'in_progress', 'medium'],
        [2, 3, 'Garbage not collected', 'Garbage has not been collected for the past week', 'Oak Lane', 'resolved', 'low'],
        [2, 4, 'Power outage in North District', 'Frequent power outages in the North District area', 'North District', 'pending', 'high']
    ];
    
    foreach ($complaints as $complaint) {
        $sql = "INSERT INTO complaints (user_id, department_id, title, description, location, status, priority) 
                VALUES ({$complaint[0]}, {$complaint[1]}, '" . $conn->real_escape_string($complaint[2]) . "', 
                '" . $conn->real_escape_string($complaint[3]) . "', '" . $conn->real_escape_string($complaint[4]) . "', 
                '" . $conn->real_escape_string($complaint[5]) . "', '" . $conn->real_escape_string($complaint[6]) . "')";
        if ($conn->query($sql) === TRUE) {
            echo "Complaint '{$complaint[2]}' created<br>";
        } else {
            echo "Error creating complaint: " . $conn->error . "<br>";
        }
    }
} else {
    echo "<h2>Sample data already exists</h2>";
    echo "Found " . $row['count'] . " users in the database<br>";
}

echo "<h2>Database initialization complete</h2>";
echo "<p><a href='index.php'>Go to homepage</a></p>";
?>