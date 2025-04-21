<?php
define('DB_SERVER', 'localhost:8889');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // Default MAMP password
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

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