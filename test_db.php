<?php
// Database credentials
define('DB_SERVER', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
echo "<h2>Testing Database Connection</h2>";
echo "<p>Attempting to connect to MySQL server at " . DB_SERVER . ":" . DB_PORT . "...</p>";

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if($conn) {
    echo "<p style='color:green;font-weight:bold;'>Connection to database successful!</p>";
} else {
    echo "<p style='color:red;font-weight:bold;'>Connection failed: " . mysqli_connect_error() . "</p>";
    
    // Try to connect without specifying a database
    echo "<p>Attempting to connect to MySQL server without specifying a database...</p>";
    $temp_conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, '', DB_PORT);
    
    if ($temp_conn) {
        echo "<p style='color:green;'>Connection to MySQL server successful!</p>";
        echo "<p>Checking if database '" . DB_NAME . "' exists...</p>";
        
        // Check if database exists
        $result = mysqli_query($temp_conn, "SHOW DATABASES LIKE '" . DB_NAME . "'");
        if (mysqli_num_rows($result) > 0) {
            echo "<p>Database '" . DB_NAME . "' exists. There might be an issue with permissions.</p>";
        } else {
            echo "<p>Database '" . DB_NAME . "' does not exist. Attempting to create it...</p>";
            
            // Try to create the database
            if (mysqli_query($temp_conn, "CREATE DATABASE IF NOT EXISTS " . DB_NAME)) {
                echo "<p style='color:green;'>Database '" . DB_NAME . "' created successfully.</p>";
                
                // Try to connect to the newly created database
                $new_conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
                if ($new_conn) {
                    echo "<p style='color:green;font-weight:bold;'>Connection to newly created database successful!</p>";
                    $conn = $new_conn; // Use this connection for the rest of the script
                } else {
                    echo "<p style='color:red;'>Failed to connect to newly created database: " . mysqli_connect_error() . "</p>";
                    exit;
                }
            } else {
                echo "<p style='color:red;'>Failed to create database: " . mysqli_error($temp_conn) . "</p>";
                exit;
            }
        }
        mysqli_close($temp_conn);
    } else {
        echo "<p style='color:red;font-weight:bold;'>Failed to connect to MySQL server: " . mysqli_connect_error() . "</p>";
        echo "<p>Please check that:</p>";
        echo "<ul>";
        echo "<li>MySQL service is running</li>";
        echo "<li>MySQL is listening on port " . DB_PORT . "</li>";
        echo "<li>Username and password are correct</li>";
        echo "</ul>";
        exit;
    }
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