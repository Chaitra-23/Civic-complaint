<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'civic_complaints';

echo "<h1>Database Connection Test</h1>";

// Test connection without database
$conn = mysqli_connect($host, $username, $password);
if (!$conn) {
    echo "<p style='color: red;'>Failed to connect to MySQL: " . mysqli_connect_error() . "</p>";
    exit();
}

echo "<p style='color: green;'>Connected to MySQL server successfully!</p>";

// Check if database exists
$result = mysqli_query($conn, "SHOW DATABASES LIKE '$database'");
if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>Database '$database' does not exist!</p>";
    
    // Create database
    echo "<p>Attempting to create database...</p>";
    if (mysqli_query($conn, "CREATE DATABASE $database")) {
        echo "<p style='color: green;'>Database created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating database: " . mysqli_error($conn) . "</p>";
        exit();
    }
} else {
    echo "<p style='color: green;'>Database '$database' exists!</p>";
}

// Select database
mysqli_select_db($conn, $database);

// Check if users table exists
$result = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($result) == 0) {
    echo "<p style='color: red;'>Table 'users' does not exist!</p>";
    
    // Create users table
    echo "<p>Attempting to create users table...</p>";
    $sql = "CREATE TABLE users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100),
        role ENUM('admin', 'staff', 'citizen') NOT NULL DEFAULT 'citizen',
        department_id INT(11),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>Table 'users' created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating table: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p style='color: green;'>Table 'users' exists!</p>";
    
    // Show table structure
    echo "<h2>Users Table Structure</h2>";
    $result = mysqli_query($conn, "DESCRIBE users");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
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
    
    // Check for admin user
    echo "<h2>Admin User Check</h2>";
    $result = mysqli_query($conn, "SELECT id, username, email, role FROM users WHERE username = 'admin'");
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        echo "<p style='color: green;'>Admin user found:</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Username: " . $user['username'] . "</li>";
        echo "<li>Email: " . $user['email'] . "</li>";
        echo "<li>Role: " . $user['role'] . "</li>";
        echo "</ul>";
        
        // Create admin user with known password
        echo "<h3>Recreating Admin User</h3>";
        mysqli_query($conn, "DELETE FROM users WHERE username = 'admin'");
        
        $username = "admin";
        $email = "admin@example.com";
        $password = password_hash("admin123", PASSWORD_DEFAULT);
        $role = "admin";
        
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>Admin user recreated successfully!</p>";
            echo "<p>Username: admin</p>";
            echo "<p>Password: admin123</p>";
            echo "<p>Password hash: $password</p>";
        } else {
            echo "<p style='color: red;'>Failed to recreate admin user: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Admin user not found!</p>";
        
        // Create admin user
        echo "<h3>Creating Admin User</h3>";
        
        $username = "admin";
        $email = "admin@example.com";
        $password = password_hash("admin123", PASSWORD_DEFAULT);
        $role = "admin";
        
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>Admin user created successfully!</p>";
            echo "<p>Username: admin</p>";
            echo "<p>Password: admin123</p>";
            echo "<p>Password hash: $password</p>";
        } else {
            echo "<p style='color: red;'>Failed to create admin user: " . mysqli_error($conn) . "</p>";
        }
    }
}

// Show all tables
echo "<h2>All Tables</h2>";
$result = mysqli_query($conn, "SHOW TABLES");
echo "<ul>";
while ($row = mysqli_fetch_array($result)) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

mysqli_close($conn);

echo "<p><a href='login.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
?>