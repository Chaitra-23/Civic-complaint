<?php
/**
 * Main Database Configuration File
 * 
 * This file contains the database connection settings for XAMPP
 * and handles database creation if it doesn't exist
 */

// Database credentials
define('DB_SERVER', '127.0.0.1');  // Using IP address to force TCP connection
define('DB_PORT', 3306);           // Default MySQL port
define('DB_USERNAME', 'root');     // Default XAMPP username
define('DB_PASSWORD', '');         // Default XAMPP has no password
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn === false){
    // Try to create the database if it doesn't exist
    $temp_conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, '', DB_PORT);
    if ($temp_conn) {
        // Create the database
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
        if (mysqli_query($temp_conn, $sql)) {
            // Connect to the newly created database
            $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
            if ($conn === false) {
                die("ERROR: Could not connect to newly created database. " . mysqli_connect_error());
            }
        } else {
            die("ERROR: Could not create database. " . mysqli_error($temp_conn));
        }
        mysqli_close($temp_conn);
    } else {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
}

// Set charset to ensure proper handling of special characters
if (!$conn->set_charset("utf8")) {
    error_log("Error setting charset: " . $conn->error);
}
?> 
