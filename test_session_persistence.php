<?php
// Start session
session_start();

echo "<h1>Session Persistence Test</h1>";

echo "<p>Current session data:</p>";
echo "<ul>";
if (isset($_SESSION['user_id'])) {
    echo "<li>user_id: " . $_SESSION['user_id'] . "</li>";
    echo "<li>username: " . $_SESSION['username'] . "</li>";
    echo "<li>email: " . $_SESSION['email'] . "</li>";
    echo "<li>role: " . $_SESSION['role'] . "</li>";
    
    echo "</ul>";
    
    echo "<p style='color: green;'>✓ Session data is preserved!</p>";
    
    // Test role check
    echo "<p>Testing role check:</p>";
    $role = strtolower($_SESSION['role']);
    if ($role === 'admin' || $role === 'administrator') {
        echo "<p style='color: green;'>✓ Role is admin, should have access to admin dashboard</p>";
        
        // Link to admin dashboard
        echo "<a href='admin/dashboard.php' style='display: inline-block; background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Dashboard</a>";
    } else {
        echo "<p style='color: red;'>✗ Role is not admin, would not have access to admin dashboard</p>";
    }
} else {
    echo "<li style='color: red;'>No session data found!</li>";
    echo "</ul>";
    
    echo "<p style='color: red;'>✗ Session data is not preserved!</p>";
}

// Show PHP session configuration
echo "<h2>PHP Session Configuration</h2>";
echo "<pre>";
echo "session.save_path: " . ini_get('session.save_path') . "\n";
echo "session.name: " . ini_get('session.name') . "\n";
echo "session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "\n";
echo "session.cookie_path: " . ini_get('session.cookie_path') . "\n";
echo "session.cookie_domain: " . ini_get('session.cookie_domain') . "\n";
echo "session.cookie_secure: " . ini_get('session.cookie_secure') . "\n";
echo "session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
echo "session.use_cookies: " . ini_get('session.use_cookies') . "\n";
echo "session.use_only_cookies: " . ini_get('session.use_only_cookies') . "\n";
echo "</pre>";
?>