<?php
echo "<h1>Admin Directory Check</h1>";

$admin_dir = __DIR__ . '/admin';
$dashboard_file = $admin_dir . '/dashboard.php';

echo "<p>Checking if admin directory exists at: $admin_dir</p>";
if (is_dir($admin_dir)) {
    echo "<p style='color: green;'>✓ Admin directory exists!</p>";
    
    echo "<p>Checking if dashboard.php exists at: $dashboard_file</p>";
    if (file_exists($dashboard_file)) {
        echo "<p style='color: green;'>✓ Dashboard file exists!</p>";
        
        echo "<p>Checking if dashboard.php is readable:</p>";
        if (is_readable($dashboard_file)) {
            echo "<p style='color: green;'>✓ Dashboard file is readable!</p>";
        } else {
            echo "<p style='color: red;'>✗ Dashboard file is not readable!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Dashboard file does not exist!</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Admin directory does not exist!</p>";
}

// Check web access
$admin_url = 'http://' . $_SERVER['HTTP_HOST'] . '/Civic/admin/dashboard.php';
echo "<p>Testing web access to: $admin_url</p>";
echo "<p>Click the link to test: <a href='$admin_url'>$admin_url</a></p>";

// List files in admin directory
if (is_dir($admin_dir)) {
    echo "<h2>Files in admin directory:</h2>";
    echo "<ul>";
    $files = scandir($admin_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}
?>