<?php
// Start session
session_start();

// Include database configuration
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct Login Test</h1>";

// Define credentials
$username = 'admin';
$password = 'admin123';

echo "<p>Attempting to log in with:</p>";
echo "<ul>";
echo "<li>Username: $username</li>";
echo "<li>Password: $password</li>";
echo "</ul>";

// Check if user exists
$sql = "SELECT id, username, email, password, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    echo "<p>User found:</p>";
    echo "<ul>";
    echo "<li>ID: {$user['id']}</li>";
    echo "<li>Username: {$user['username']}</li>";
    echo "<li>Email: {$user['email']}</li>";
    echo "<li>Role: {$user['role']}</li>";
    echo "<li>Password Hash: {$user['password']}</li>";
    echo "</ul>";
    
    // Test password verification
    echo "<h2>Password Verification Test</h2>";
    
    $verification_result = password_verify($password, $user['password']);
    echo "<p>password_verify() result: " . ($verification_result ? "TRUE" : "FALSE") . "</p>";
    
    if ($verification_result) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        echo "<div style='padding: 15px; background-color: #d4edda; color: #155724; border-radius: 5px;'>";
        echo "<h3>Login Successful!</h3>";
        echo "<p>You are now logged in as {$user['username']} with role {$user['role']}.</p>";
        echo "<p><a href='index.php'>Go to Homepage</a></p>";
        echo "</div>";
    } else {
        echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>";
        echo "<h3>Login Failed</h3>";
        echo "<p>Password verification failed. Let's try to understand why:</p>";
        
        // Additional debugging
        echo "<h4>Hash Information</h4>";
        $hash_info = password_get_info($user['password']);
        echo "<pre>";
        print_r($hash_info);
        echo "</pre>";
        
        // Try different password variations
        echo "<h4>Testing Different Password Variations</h4>";
        $variations = [
            'admin123' => password_verify('admin123', $user['password']),
            'admin123 ' => password_verify('admin123 ', $user['password']), // with trailing space
            ' admin123' => password_verify(' admin123', $user['password']), // with leading space
            'Admin123' => password_verify('Admin123', $user['password']), // with capital A
            'password123' => password_verify('password123', $user['password']), // default from setup
        ];
        
        echo "<ul>";
        foreach ($variations as $pwd => $result) {
            echo "<li>'$pwd': " . ($result ? "WORKS" : "fails") . "</li>";
        }
        echo "</ul>";
        
        echo "</div>";
    }
} else {
    echo "<div style='padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>";
    echo "<h3>User Not Found</h3>";
    echo "<p>No user found with username '$username'.</p>";
    echo "</div>";
}

// Close connection
$stmt->close();
$conn->close();

echo "<p><a href='fix_admin_login.php'>Run Admin Login Fix</a></p>";
?>