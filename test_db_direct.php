<?php
// Database credentials
$server = '127.0.0.1:3307';  // Using IP instead of 'localhost' with correct port 3307
$username = 'root';
$password = ''; // Empty password for XAMPP default
$database = 'civic_complaints';

echo "Attempting to connect to MySQL server at $server...\n";

// Try connecting without database first
$conn = mysqli_connect($server, $username, $password);
if ($conn) {
    echo "Successfully connected to MySQL server!\n";
    
    // Check if database exists
    $result = mysqli_query($conn, "SHOW DATABASES LIKE '$database'");
    if (mysqli_num_rows($result) > 0) {
        echo "Database '$database' exists.\n";
        
        // Try connecting to the specific database
        $db_conn = mysqli_connect($server, $username, $password, $database);
        if ($db_conn) {
            echo "Successfully connected to database '$database'!\n";
            mysqli_close($db_conn);
        } else {
            echo "Failed to connect to database '$database': " . mysqli_connect_error() . "\n";
        }
    } else {
        echo "Database '$database' does not exist.\n";
        echo "Attempting to create database '$database'...\n";
        
        if (mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS $database")) {
            echo "Database created successfully!\n";
        } else {
            echo "Error creating database: " . mysqli_error($conn) . "\n";
        }
    }
    
    mysqli_close($conn);
} else {
    echo "Failed to connect to MySQL server: " . mysqli_connect_error() . "\n";
    
    // Check if the port is different
    echo "Trying alternate port (3307 explicitly)...\n";
    $alternate_server = '127.0.0.1:3307';
    $alt_conn = mysqli_connect($alternate_server, $username, $password);
    if ($alt_conn) {
        echo "Successfully connected to MySQL server at $alternate_server!\n";
        mysqli_close($alt_conn);
    } else {
        echo "Failed to connect to alternate port: " . mysqli_connect_error() . "\n";
    }
}
?>