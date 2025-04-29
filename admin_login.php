<?php
// Start session
session_start();

// Include database connection
require_once 'config/database.php';

echo "<h1>Direct Admin Login</h1>";

// Get admin user
$sql = "SELECT id, username, email, role, password FROM users WHERE username = 'admin'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    echo "<p>Session variables set:</p>";
    echo "<ul>";
    echo "<li>user_id: " . $_SESSION['user_id'] . "</li>";
    echo "<li>username: " . $_SESSION['username'] . "</li>";
    echo "<li>email: " . $_SESSION['email'] . "</li>";
    echo "<li>role: " . $_SESSION['role'] . "</li>";
    echo "</ul>";
    
    echo "<p>Click the button below to go to the admin dashboard:</p>";
    echo "<a href='admin/dashboard.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a>";
} else {
    echo "<p style='color: red;'>Admin user not found!</p>";
    echo "<a href='test_admin.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Create Admin User</a>";
}
?>