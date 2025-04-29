<?php
// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connectivity Test</h1>";

// Test 1: Direct connection using mysqli
echo "<h2>Test 1: Direct Connection Test</h2>";
$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';
$db = 'civic_complaints';

echo "Attempting to connect to MySQL at $host:$port as user '$user'...<br>";
$mysqli = @new mysqli($host, $user, $pass, $db, $port);

if ($mysqli->connect_error) {
    echo "<span style='color:red'>Connection failed: " . $mysqli->connect_error . "</span><br>";
    
    // Try connecting without database to see if MySQL server is accessible
    echo "Trying to connect to MySQL server without specifying database...<br>";
    $mysqli = @new mysqli($host, $user, $pass, '', $port);
    
    if ($mysqli->connect_error) {
        echo "<span style='color:red'>MySQL server connection failed: " . $mysqli->connect_error . "</span><br>";
    } else {
        echo "<span style='color:green'>Connected to MySQL server successfully!</span><br>";
        echo "Database '$db' might not exist. Checking available databases:<br>";
        
        $result = $mysqli->query("SHOW DATABASES");
        if ($result) {
            echo "<ul>";
            $db_exists = false;
            while ($row = $result->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
                if ($row[0] == $db) {
                    $db_exists = true;
                }
            }
            echo "</ul>";
            
            if (!$db_exists) {
                echo "<span style='color:orange'>Database '$db' does not exist. Attempting to create it...</span><br>";
                if ($mysqli->query("CREATE DATABASE IF NOT EXISTS `$db`")) {
                    echo "<span style='color:green'>Database created successfully!</span><br>";
                } else {
                    echo "<span style='color:red'>Failed to create database: " . $mysqli->error . "</span><br>";
                }
            }
        } else {
            echo "<span style='color:red'>Error listing databases: " . $mysqli->error . "</span><br>";
        }
        
        $mysqli->close();
    }
} else {
    echo "<span style='color:green'>Connected to MySQL database successfully!</span><br>";
    echo "MySQL version: " . $mysqli->server_info . "<br>";
    
    // Check tables
    echo "<h3>Checking database tables:</h3>";
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<span style='color:orange'>No tables found in the database.</span><br>";
        }
    } else {
        echo "<span style='color:red'>Error listing tables: " . $mysqli->error . "</span><br>";
    }
    
    $mysqli->close();
}

// Test 2: Using the project's database.php file
echo "<h2>Test 2: Project Configuration Test</h2>";
echo "Including database.php from project...<br>";

// Save current error reporting level
$old_error_level = error_reporting();
$old_display_errors = ini_get('display_errors');

// Temporarily disable error reporting to catch any errors manually
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any output or errors
ob_start();
$include_result = include 'config/database.php';
$output = ob_get_clean();

// Restore error reporting
error_reporting($old_error_level);
ini_set('display_errors', $old_display_errors);

if (!$include_result) {
    echo "<span style='color:red'>Failed to include database.php</span><br>";
    if ($output) {
        echo "Output/Errors: <pre>" . htmlspecialchars($output) . "</pre>";
    }
} else {
    echo "<span style='color:green'>database.php included successfully</span><br>";
    if ($output) {
        echo "Output: <pre>" . htmlspecialchars($output) . "</pre>";
    }
    
    if (isset($conn) && $conn) {
        echo "<span style='color:green'>Database connection established via project configuration!</span><br>";
        
        // Test a simple query
        $test_query = "SELECT 1 as test";
        $result = mysqli_query($conn, $test_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "Test query result: " . $row['test'] . "<br>";
            
            // Check tables
            echo "<h3>Tables in database:</h3>";
            $tables_result = mysqli_query($conn, "SHOW TABLES");
            if ($tables_result) {
                if (mysqli_num_rows($tables_result) > 0) {
                    echo "<ul>";
                    while ($table_row = mysqli_fetch_row($tables_result)) {
                        echo "<li>" . $table_row[0] . "</li>";
                    }
                    echo "</ul>";
                    
                    // Check for specific required tables
                    $required_tables = ['users', 'complaints', 'departments'];
                    echo "<h3>Checking required tables:</h3>";
                    
                    mysqli_data_seek($tables_result, 0);
                    $existing_tables = [];
                    while ($table_row = mysqli_fetch_row($tables_result)) {
                        $existing_tables[] = $table_row[0];
                    }
                    
                    foreach ($required_tables as $table) {
                        if (in_array($table, $existing_tables)) {
                            echo "<span style='color:green'>✓ Table '$table' exists</span><br>";
                            
                            // Check table structure
                            $structure_result = mysqli_query($conn, "DESCRIBE `$table`");
                            if ($structure_result) {
                                echo "<details><summary>$table structure</summary><ul>";
                                while ($field = mysqli_fetch_assoc($structure_result)) {
                                    echo "<li>" . $field['Field'] . " (" . $field['Type'] . ")</li>";
                                }
                                echo "</ul></details>";
                            }
                            
                            // Check record count
                            $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM `$table`");
                            if ($count_result) {
                                $count_row = mysqli_fetch_assoc($count_result);
                                echo "Records in $table: " . $count_row['count'] . "<br>";
                            }
                        } else {
                            echo "<span style='color:red'>✗ Table '$table' does not exist</span><br>";
                        }
                    }
                } else {
                    echo "<span style='color:orange'>No tables found in the database.</span><br>";
                }
            } else {
                echo "<span style='color:red'>Error listing tables: " . mysqli_error($conn) . "</span><br>";
            }
        } else {
            echo "<span style='color:red'>Error executing test query: " . mysqli_error($conn) . "</span><br>";
        }
        
        // Close the connection
        mysqli_close($conn);
    } else {
        echo "<span style='color:red'>Database connection failed using project configuration.</span><br>";
        echo "Connection error: " . mysqli_connect_error() . "<br>";
    }
}

// Test 3: Check if the connection string format is correct
echo "<h2>Test 3: Connection String Format Test</h2>";

// Test with separate host and port
echo "Testing connection with separate host and port parameters...<br>";
$host = '127.0.0.1';
$port = 3307;
$mysqli_separate = @new mysqli($host, $user, $pass, $db, $port);

if ($mysqli_separate->connect_error) {
    echo "<span style='color:red'>Connection with separate host/port failed: " . $mysqli_separate->connect_error . "</span><br>";
} else {
    echo "<span style='color:green'>Connection with separate host/port successful!</span><br>";
    $mysqli_separate->close();
}

// Test with combined host:port format
echo "Testing connection with combined 'host:port' format...<br>";
$host_port = '127.0.0.1:3307';
$mysqli_combined = @mysqli_connect($host_port, $user, $pass, $db);

if (!$mysqli_combined) {
    echo "<span style='color:red'>Connection with combined host:port failed: " . mysqli_connect_error() . "</span><br>";
    
    echo "Recommendation: Update database.php to use separate host and port parameters:<br>";
    echo "<pre>
define('DB_SERVER', '127.0.0.1');
define('DB_PORT', 3307);
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'civic_complaints');

// Attempt to connect to MySQL database
\$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
</pre>";
} else {
    echo "<span style='color:green'>Connection with combined host:port successful!</span><br>";
    mysqli_close($mysqli_combined);
}

echo "<h2>Summary</h2>";
echo "MySQL server is running on port 3307<br>";
echo "Check the test results above for any connection issues<br>";
echo "If you're experiencing problems, consider updating your connection parameters based on the successful tests<br>";
?>