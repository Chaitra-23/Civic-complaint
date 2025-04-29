<?php
// Start session
session_start();

// Get POST data
$username = $_POST['username'] ?? '';
$role = $_POST['role'] ?? '';

echo "<h1>Login Redirect Test</h1>";
echo "<p>Testing redirect for user: $username with role: $role</p>";

// Set session variables (simulating login)
$_SESSION['user_id'] = 1; // Dummy ID
$_SESSION['username'] = $username;
$_SESSION['email'] = 'test@example.com'; // Dummy email
$_SESSION['role'] = $role;

echo "<p>Session variables set:</p>";
echo "<ul>";
echo "<li>user_id: " . $_SESSION['user_id'] . "</li>";
echo "<li>username: " . $_SESSION['username'] . "</li>";
echo "<li>email: " . $_SESSION['email'] . "</li>";
echo "<li>role: " . $_SESSION['role'] . "</li>";
echo "</ul>";

// Test the redirect logic
echo "<p>Testing redirect logic:</p>";
$role = strtolower($_SESSION['role']);
if ($role === 'admin' || $role === 'administrator') {
    echo "<p style='color: green;'>Role is admin, should redirect to: admin/dashboard.php</p>";
    echo "<p>Executing redirect code:</p>";
    echo "<pre>window.location.href = 'admin/dashboard.php';</pre>";
    echo "<p>Click the button to manually redirect:</p>";
    echo "<a href='admin/dashboard.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a>";
} else {
    echo "<p style='color: red;'>Role is not admin, would redirect to: index.php</p>";
    echo "<p>Executing redirect code:</p>";
    echo "<pre>window.location.href = 'index.php';</pre>";
    echo "<p>Click the button to manually redirect:</p>";
    echo "<a href='index.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Home Page</a>";
}
?>