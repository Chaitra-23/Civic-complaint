<?php
// Include header which should establish database connection
require_once 'includes/header.php';

// Check if connection is successful
if ($conn) {
    echo "<h2>Database Connection from header.php is Successful</h2>";
    
    // Test a simple query
    $result = $conn->query("SELECT COUNT(*) as count FROM complaints");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total complaints: " . $row['count'] . "</p>";
    } else {
        echo "<p>Error querying complaints: " . $conn->error . "</p>";
    }
} else {
    echo "<h2>Database Connection from header.php Failed</h2>";
}
?>

<a href="index.php">Return to Homepage</a>