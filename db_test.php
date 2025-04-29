<?php
// Include the main database configuration file
require_once 'config/database.php';

// Test connection
if ($conn) {

echo "Connected successfully to database<br>";

// Test queries
echo "<h2>Database Test Results:</h2>";

// Test complaints table
$sql = "SELECT COUNT(*) as count FROM complaints";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total complaints: " . $row['count'] . "<br>";
} else {
    echo "Error querying complaints: " . $conn->error . "<br>";
}

// Test resolved complaints
$sql = "SELECT COUNT(*) as count FROM complaints WHERE status = 'resolved'";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Resolved complaints: " . $row['count'] . "<br>";
} else {
    echo "Error querying resolved complaints: " . $conn->error . "<br>";
}

// Test departments
$sql = "SELECT COUNT(*) as count FROM departments";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Departments: " . $row['count'] . "<br>";
} else {
    echo "Error querying departments: " . $conn->error . "<br>";
}

// Test users
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Users: " . $row['count'] . "<br>";
} else {
    echo "Error querying users: " . $conn->error . "<br>";
}

// Close connection
$conn->close();
}
?>