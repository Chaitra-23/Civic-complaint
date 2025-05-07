<?php
// Database credentials
define('DB_SERVER', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'civic_complaints');

echo "Attempting to connect to database...<br>";

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn === false){
    echo "ERROR: Could not connect. " . mysqli_connect_error() . "<br>";
} else {
    echo "Connected successfully!<br>";
    echo "Server info: " . $conn->server_info . "<br>";
    echo "Host info: " . $conn->host_info . "<br>";
    
    // Test query
    echo "<h2>Testing queries:</h2>";
    
    // Test complaints table
    $sql = "SELECT COUNT(*) as count FROM complaints";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total complaints: " . $row['count'] . "<br>";
    } else {
        echo "Error querying complaints: " . $conn->error . "<br>";
    }
    
    // Test departments table
    $sql = "SELECT COUNT(*) as count FROM departments";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total departments: " . $row['count'] . "<br>";
    } else {
        echo "Error querying departments: " . $conn->error . "<br>";
    }
    
    // Test users table
    $sql = "SELECT COUNT(*) as count FROM users";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total users: " . $row['count'] . "<br>";
    } else {
        echo "Error querying users: " . $conn->error . "<br>";
    }
}
?>