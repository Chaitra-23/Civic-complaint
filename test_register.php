<?php
// Include database configuration
require_once 'config/database.php';

// Test data
$username = "testuser";
$email = "testuser@example.com";
$password = "password123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$full_name = "Test User";

// Insert user into database
$sql = "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ssss", $username, $email, $hashed_password, $full_name);

if ($stmt->execute()) {
    echo "User registered successfully!";
} else {
    echo "Error executing statement: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>