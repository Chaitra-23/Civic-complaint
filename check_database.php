<?php
// Database credentials
$servername = "127.0.0.1";
$port = 3307;
$username = "root";
$password = ""; // Empty password for XAMPP default

// Create connection without specifying a database
$conn = new mysqli($servername, $username, $password, "", $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL server successfully.<br>";

// Check if civic_complaints database exists
$result = $conn->query("SHOW DATABASES LIKE 'civic_complaints'");
if ($result->num_rows > 0) {
    echo "Database 'civic_complaints' exists.<br>";
} else {
    echo "Database 'civic_complaints' does not exist. Creating it now...<br>";
    
    // Create the database
    if ($conn->query("CREATE DATABASE civic_complaints")) {
        echo "Database created successfully.<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }
}

// Close the connection
$conn->close();

echo "<p>Next steps:</p>";
echo "<ol>";
echo "<li><a href='setup_database.php'>Run database setup script</a> (creates tables and sample data)</li>";
echo "<li><a href='create_admin.php'>Create/reset admin user</a></li>";
echo "<li><a href='login.php'>Go to login page</a></li>";
echo "</ol>";
?>