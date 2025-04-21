<?php
define('DB_SERVER', 'localhost:8889');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // Default MAMP password
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
} else {
    echo "Connected successfully to the database!<br>";
    
    // Check if admin user exists
    $sql = "SELECT id, username, email, role FROM users WHERE username = 'admin'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "Admin user found:<br>";
        echo "ID: " . $row["id"] . "<br>";
        echo "Username: " . $row["username"] . "<br>";
        echo "Email: " . $row["email"] . "<br>";
        echo "Role: " . $row["role"] . "<br>";
    } else {
        echo "Admin user not found.";
    }
}
?>