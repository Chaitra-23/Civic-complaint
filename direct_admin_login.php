<?php
// Start session
session_start();

// Include database connection
require_once 'config/database.php';

// Admin credentials
$username = 'admin'; // or 'chaitra' if you want to use that account
$password = 'admin123'; // replace with the actual password if different

// Get user from database
$sql = "SELECT id, username, email, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Direct Admin Login Test</h1>";

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    echo "<p>User found in database:</p>";
    echo "<ul>";
    echo "<li>ID: " . $user['id'] . "</li>";
    echo "<li>Username: " . $user['username'] . "</li>";
    echo "<li>Email: " . $user['email'] . "</li>";
    echo "<li>Role: " . $user['role'] . "</li>";
    echo "<li>Password Hash: " . substr($user['password'], 0, 20) . "...</li>";
    echo "</ul>";
    
    // Clear any existing session data
    session_unset();
    
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
    
    echo "<p>Or click below to test the regular login redirect:</p>";
    echo "<form action='login_redirect_test.php' method='post'>";
    echo "<input type='hidden' name='username' value='" . $user['username'] . "'>";
    echo "<input type='hidden' name='role' value='" . $user['role'] . "'>";
    echo "<button type='submit' style='background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Login Redirect</button>";
    echo "</form>";
} else {
    echo "<p style='color: red;'>Admin user not found!</p>";
}
?>