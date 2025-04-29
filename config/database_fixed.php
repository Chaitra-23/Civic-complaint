<?php
/**
 * Fixed Database Configuration File
 * 
 * This file contains the optimized database connection settings for XAMPP
 */

// Database credentials
define('DB_SERVER', '127.0.0.1');  // Using IP instead of 'localhost' to force TCP connection (needed for custom port)
define('DB_PORT', 3307);           // Your MySQL is running on port 3307
define('DB_USERNAME', 'root');     // Default XAMPP username
define('DB_PASSWORD', '');         // Default XAMPP has no password
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn === false){
    // Log the error
    error_log("Database connection failed: " . mysqli_connect_error());
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Set charset to ensure proper handling of special characters
if (!$conn->set_charset("utf8")) {
    error_log("Error setting charset: " . $conn->error);
}

// Test connection with a simple query
$test_query = "SELECT 1";
$test_result = $conn->query($test_query);
if (!$test_result) {
    error_log("Database test query failed: " . $conn->error);
}

// Log successful connection
error_log("Database connected successfully");
?>
