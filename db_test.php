<?php
// Direct database connection test
define('DB_SERVER', 'localhost:8889');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // Default MAMP password
define('DB_NAME', 'civic_complaints');

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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
?>