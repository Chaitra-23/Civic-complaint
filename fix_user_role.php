<?php
// Include database connection
require_once 'config/database.php';

// Update the role for user 'chaitra'
$sql = "UPDATE users SET role = 'admin' WHERE username = 'chaitra'";
if ($conn->query($sql)) {
    echo "User role updated successfully!<br>";
    echo "Username: chaitra<br>";
    echo "New Role: admin<br>";
    echo "<a href='login.php'>Go to Login Page</a>";
} else {
    echo "Error updating user role: " . $conn->error;
}

$conn->close();
?>