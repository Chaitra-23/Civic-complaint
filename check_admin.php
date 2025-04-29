<?php
// Include database configuration
require_once 'config/database.php';

// Check if admin user exists and display password hash
$check_sql = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($check_sql);

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "Admin user found:<br>";
    echo "ID: " . $admin['id'] . "<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Role: " . $admin['role'] . "<br>";
    echo "Password Hash: " . $admin['password'] . "<br>";
    
    // Test if 'password123' would verify against this hash
    $test_password = 'password123';
    if (password_verify($test_password, $admin['password'])) {
        echo "<p>The password 'password123' would work with this hash.</p>";
    } else {
        echo "<p>The password 'password123' would NOT work with this hash.</p>";
    }
    
    // Test if 'admin123' would verify against this hash
    $test_password = 'admin123';
    if (password_verify($test_password, $admin['password'])) {
        echo "<p>The password 'admin123' would work with this hash.</p>";
    } else {
        echo "<p>The password 'admin123' would NOT work with this hash.</p>";
    }
    
} else {
    echo "Admin user not found in the database.";
}

// Close connection
$conn->close();

echo "<p><a href='create_admin.php'>Create/Reset Admin User</a></p>";
?>