<?php
require_once 'config/database.php';

// Check if admin user exists
$sql = "SELECT id, username, email, role, password FROM users WHERE username = 'admin'";
$result = $conn->query($sql);

echo "<h1>Admin User Test</h1>";

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<p>Admin user found:</p>";
    echo "<ul>";
    echo "<li>ID: " . $user['id'] . "</li>";
    echo "<li>Username: " . $user['username'] . "</li>";
    echo "<li>Email: " . $user['email'] . "</li>";
    echo "<li>Role: " . $user['role'] . "</li>";
    echo "<li>Password Hash: " . $user['password'] . "</li>";
    echo "</ul>";
    
    // Test password verification
    $test_password = "admin123";
    $verification_result = password_verify($test_password, $user['password']);
    
    echo "<p>Password verification test:</p>";
    echo "<ul>";
    echo "<li>Test password: " . $test_password . "</li>";
    echo "<li>Verification result: " . ($verification_result ? "Success" : "Failed") . "</li>";
    echo "</ul>";
    
    if (!$verification_result) {
        echo "<p style='color: red;'>The password hash in the database doesn't match the expected password.</p>";
        
        // Create a new password hash for comparison
        $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "<p>New hash for 'admin123': " . $new_hash . "</p>";
        echo "<p>Verification with new hash: " . (password_verify($test_password, $new_hash) ? "Success" : "Failed") . "</p>";
    }
} else {
    echo "<p style='color: red;'>Admin user not found in the database!</p>";
    
    // Try to create admin user
    echo "<h2>Creating admin user...</h2>";
    
    $username = "admin";
    $email = "admin@example.com";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $role = "admin";
    
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $role);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Admin user created successfully!</p>";
        echo "<p>Username: admin</p>";
        echo "<p>Password: admin123</p>";
    } else {
        echo "<p style='color: red;'>Failed to create admin user: " . $conn->error . "</p>";
    }
}

// Check database tables
echo "<h2>Database Tables</h2>";
$tables_result = $conn->query("SHOW TABLES");
if ($tables_result) {
    echo "<ul>";
    while ($table = $tables_result->fetch_array()) {
        echo "<li>" . $table[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Error fetching tables: " . $conn->error . "</p>";
}

// Test session
echo "<h2>Session Test</h2>";
session_start();
$_SESSION['test_value'] = "This is a test session value";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session test value set. Refresh to see if it persists.</p>";

if (isset($_SESSION['test_value'])) {
    echo "<p>Current test value: " . $_SESSION['test_value'] . "</p>";
}
?>

<p><a href="login.php">Go to Login Page</a></p>