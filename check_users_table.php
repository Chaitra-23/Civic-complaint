<?php
// Include database configuration
require_once 'config/database.php';

// Check the structure of the users table
$check_sql = "DESCRIBE users";
$result = $conn->query($check_sql);

if ($result) {
    echo "<h2>Users Table Structure</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Error checking users table: " . $conn->error;
}

// Check if there are any users in the table
$users_sql = "SELECT id, username, email, role FROM users";
$users_result = $conn->query($users_sql);

if ($users_result) {
    echo "<h2>Existing Users</h2>";
    
    if ($users_result->num_rows > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";
        
        while ($user = $users_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "No users found in the database.";
    }
} else {
    echo "Error retrieving users: " . $conn->error;
}

// Close connection
$conn->close();

echo "<p><a href='create_admin.php'>Create/Reset Admin User</a></p>";
?>