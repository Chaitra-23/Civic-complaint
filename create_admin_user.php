<?php
// Include database connection
require_once 'config/database.php';

// Admin user details
$username = 'admin';
$email = 'admin@example.com';
$password = 'admin123'; // This will be hashed
$full_name = 'Admin User';
$role = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if user already exists
$check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $username, $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Admin user already exists!";
} else {
    // Insert admin user
    $sql = "INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $role);
    
    if ($stmt->execute()) {
        echo "Admin user created successfully!<br>";
        echo "Username: $username<br>";
        echo "Password: $password<br>";
        echo "Role: $role<br>";
        echo "<a href='login.php'>Go to Login Page</a>";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
}

// Create a department if none exists
$dept_sql = "SELECT id FROM departments LIMIT 1";
$dept_result = $conn->query($dept_sql);

if ($dept_result->num_rows == 0) {
    $dept_insert = "INSERT INTO departments (name, description) VALUES ('Public Works', 'Responsible for infrastructure maintenance and public facilities')";
    if ($conn->query($dept_insert)) {
        echo "<br>Default department created successfully!";
    } else {
        echo "<br>Error creating department: " . $conn->error;
    }
}

$conn->close();
?>