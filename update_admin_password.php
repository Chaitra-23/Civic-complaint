<?php
// Include database connection
require_once 'config/database.php';

// Admin credentials
$username = 'admin';
$new_password = 'admin123';

// Hash the new password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the password in the database
$sql = "UPDATE users SET password = ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashed_password, $username);

if ($stmt->execute()) {
    echo "Password updated successfully for user: $username<br>";
    echo "New password: $new_password<br>";
    echo "Hashed password: $hashed_password<br>";
    echo "<a href='login.php'>Go to Login Page</a>";
} else {
    echo "Error updating password: " . $stmt->error;
}

$conn->close();
?>