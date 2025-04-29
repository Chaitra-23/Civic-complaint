<?php
// Include database configuration
require_once 'config/database.php';

// Check connection
if ($conn) {
    echo "Database connection: Success<br>";
    
    // Get database name
    $result = $conn->query("SELECT DATABASE()");
    $row = $result->fetch_row();
    echo "Current database: " . $row[0] . "<br>";
    
    // Show tables
    echo "Tables in database:<br>";
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        while ($row = $result->fetch_row()) {
            echo "- " . $row[0] . "<br>";
        }
    } else {
        echo "Error showing tables: " . $conn->error . "<br>";
    }
    
    // Check users table structure
    echo "<br>Users table structure:<br>";
    $result = $conn->query("DESCRIBE users");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
        }
    } else {
        echo "Error describing users table: " . $conn->error . "<br>";
    }
    
    // Test prepare statement
    echo "<br>Testing prepare statement:<br>";
    $sql = "INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        echo "Prepare statement successful<br>";
        $stmt->close();
    }
    
} else {
    echo "Database connection failed: " . mysqli_connect_error();
}

// Close connection
if ($conn) {
    $conn->close();
}
?>