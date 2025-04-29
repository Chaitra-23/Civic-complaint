<?php
// Include database connection
require_once 'config/database.php';

// Check all users
$sql = "SELECT id, username, email, role, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Users in the database:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Password Hash (first 20 chars)</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>" . substr($row['password'], 0, 20) . "...</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No users found in the database.";
}

$conn->close();
?>