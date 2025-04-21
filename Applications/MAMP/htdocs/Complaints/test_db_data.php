<?php
// Include database connection
require_once 'config/database.php';

// Check if connection is successful
if ($conn) {
    echo "<h2>Database Connection Successful</h2>";
} else {
    echo "<h2>Database Connection Failed</h2>";
    exit;
}

// Check if tables exist and have data
echo "<h3>Database Tables and Data:</h3>";

// Check users table
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>Users: " . $row['count'] . "</p>";
} else {
    echo "<p>Error querying users table: " . $conn->error . "</p>";
}

// Check departments table
$sql = "SELECT COUNT(*) as count FROM departments";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>Departments: " . $row['count'] . "</p>";
} else {
    echo "<p>Error querying departments table: " . $conn->error . "</p>";
}

// Check complaints table
$sql = "SELECT COUNT(*) as count FROM complaints";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>Complaints: " . $row['count'] . "</p>";
} else {
    echo "<p>Error querying complaints table: " . $conn->error . "</p>";
}

// If complaints exist, show some details
if ($result && $row['count'] > 0) {
    echo "<h3>Complaint Details:</h3>";
    $sql = "SELECT c.id, c.title, c.status, u.username, d.name as department 
            FROM complaints c 
            JOIN users u ON c.user_id = u.id 
            JOIN departments d ON c.department_id = d.id 
            LIMIT 5";
    $result = $conn->query($sql);
    if ($result) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>User</th><th>Department</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['department'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Error querying complaint details: " . $conn->error . "</p>";
    }
}
?>