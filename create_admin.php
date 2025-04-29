<?php
// Include database configuration
require_once 'config/database.php';

// Define admin credentials
$admin_username = 'admin';
$admin_password = 'admin123';
$admin_email = 'admin@example.com';
$admin_fullname = 'Administrator';
$admin_role = 'admin';

// Generate password hash
$password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

// Check if admin user exists
$check_sql = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($check_sql);

if ($result && $result->num_rows > 0) {
    echo "<h2>Admin User Reset</h2>";
    $admin = $result->fetch_assoc();
    echo "Existing Admin ID: " . $admin['id'] . "<br>";
    echo "Username: " . $admin['username'] . "<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Role: " . $admin['role'] . "<br><br>";
    
    // Delete the existing admin user
    $delete_sql = "DELETE FROM users WHERE username = 'admin'";
    if ($conn->query($delete_sql)) {
        echo "Existing admin user deleted.<br>";
    } else {
        echo "Error deleting admin user: " . $conn->error . "<br>";
        exit;
    }
    
    // Create a new admin user
    $insert_sql = "INSERT INTO users (username, password, email, full_name, role) 
                  VALUES ('$admin_username', '$password_hash', '$admin_email', '$admin_fullname', '$admin_role')";
    
    if ($conn->query($insert_sql)) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>Success!</strong> Admin user has been recreated with a new password.<br>";
        echo "Username: $admin_username<br>";
        echo "Password: $admin_password<br>";
        echo "</div>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    echo "<h2>Create Admin User</h2>";
    
    // Create admin user
    $insert_sql = "INSERT INTO users (username, password, email, full_name, role) 
                  VALUES ('$admin_username', '$password_hash', '$admin_email', '$admin_fullname', '$admin_role')";
    
    if ($conn->query($insert_sql)) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>Success!</strong> Admin user created successfully!<br>";
        echo "Username: $admin_username<br>";
        echo "Password: $admin_password<br>";
        echo "</div>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
}

// Verify the password hash works with password_verify
echo "<h3>Password Verification Test</h3>";
echo "Password Hash: $password_hash<br>";
if (password_verify($admin_password, $password_hash)) {
    echo "Password verification successful! The hash is valid.<br>";
} else {
    echo "Password verification failed! There might be an issue with the hashing.<br>";
}

// Close connection
$conn->close();

echo "<p><a href='login.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
?>