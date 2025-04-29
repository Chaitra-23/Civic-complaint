<?php
// Start session
session_start();

// Include database connection
require_once 'config/database.php';

// Set admin session variables directly
$_SESSION['user_id'] = 1; // This should be the admin user ID
$_SESSION['username'] = 'admin';
$_SESSION['email'] = 'admin@example.com';
$_SESSION['role'] = 'admin';

echo "<h1>Admin Login Bypass</h1>";
echo "<p>Session variables set:</p>";
echo "<ul>";
echo "<li>user_id: " . $_SESSION['user_id'] . "</li>";
echo "<li>username: " . $_SESSION['username'] . "</li>";
echo "<li>email: " . $_SESSION['email'] . "</li>";
echo "<li>role: " . $_SESSION['role'] . "</li>";
echo "</ul>";

echo "<p>Click the button below to go to the admin dashboard:</p>";
echo "<a href='admin/dashboard.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a>";
?>