<?php
// Start session
session_start();

// Include database connection
require_once 'config/database.php';

// Admin credentials
$username = 'admin'; // or 'chaitra' if you want to use that account
$password = 'admin123'; // replace with the actual password if different

echo "<h1>Login Process Test</h1>";

// Get user from database
$sql = "SELECT id, username, email, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

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
    
    // Test password verification
    echo "<p>Testing password verification:</p>";
    if (password_verify($password, $user['password'])) {
        echo "<p style='color: green;'>✓ Password verification successful!</p>";
        
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
        
        // Test role check
        echo "<p>Testing role check:</p>";
        $role = strtolower($_SESSION['role']);
        if ($role === 'admin' || $role === 'administrator') {
            echo "<p style='color: green;'>✓ Role is admin, should redirect to admin/dashboard.php</p>";
            
            // Test session persistence
            echo "<p>Testing session persistence:</p>";
            echo "<a href='test_session_persistence.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Test Session Persistence</a>";
            
            // Link to admin dashboard
            echo "<a href='admin/dashboard.php' style='display: inline-block; background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a>";
        } else {
            echo "<p style='color: red;'>✗ Role is not admin, would redirect to index.php</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Password verification failed!</p>";
        echo "<p>Expected password: $password</p>";
        echo "<p>Stored hash: " . $user['password'] . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ User not found!</p>";
}
?>