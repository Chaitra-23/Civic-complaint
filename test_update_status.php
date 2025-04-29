<?php
// Start a session to simulate being logged in as admin
session_start();
$_SESSION['user_id'] = 1; // Assuming user ID 1 is an admin
$_SESSION['role'] = 'admin';
$_SESSION['username'] = 'admin'; // Add username for header.php

// Set up GET parameter to simulate accessing a complaint
$_GET['id'] = 1; // Assuming complaint ID 1 exists

// Capture output
ob_start();

// Include the update_status.php file
echo "Testing admin/update_status.php:\n";
try {
    // Define the base path for includes
    $_SERVER['PHP_SELF'] = '/admin/update_status.php';
    
    // Include the file
    require_once 'admin/update_status.php';
    echo "Test completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get output
$output = ob_get_clean();
echo $output;
?>