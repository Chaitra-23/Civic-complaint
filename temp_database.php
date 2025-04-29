<?php
/**
 * Database Configuration File
 * 
 * This file contains the database connection settings for XAMPP
 */

// Database credentials
define('DB_SERVER', 'localhost');  // Using 'localhost' for XAMPP
define('DB_PORT', 3307);           // Your MySQL is running on port 3307
define('DB_USERNAME', 'root');     // Default XAMPP username
define('DB_PASSWORD', '');         // Default XAMPP has no password
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn === false){
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}

// Set charset to ensure proper handling of special characters
if (!$conn->set_charset("utf8")) {
    error_log("Error setting charset: " . $conn->error);
}
?>
