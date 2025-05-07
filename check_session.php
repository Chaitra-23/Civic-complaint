<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

echo "<h1>Session Check</h1>";

// Display session information
echo "<h2>Session Information</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Display cookie information
echo "<h2>Cookie Information</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

// Display server information
echo "<h2>Server Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Server Port: " . $_SERVER['SERVER_PORT'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Remote Address: " . $_SERVER['REMOTE_ADDR'] . "</p>";
echo "<p>User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "</p>";

// Display links
echo "<h2>Links</h2>";
echo "<p><a href='index.php'>Go to index.php</a></p>";
echo "<p><a href='index.php?debug=1'>Go to index.php with debug</a></p>";
echo "<p><a href='clear_cache.php'>Clear cache and go to index.php</a></p>";
echo "<p><a href='verify_index.php'>Verify index stats</a></p>";
?>