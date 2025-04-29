<?php
// Start session
session_start();

// Include database configuration
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Login Fix</h1>";

// Step 1: Check database connection
echo "<h2>Step 1: Database Connection</h2>";
if ($conn) {
    echo "<p style='color:green'>✓ Database connection successful</p>";
} else {
    echo "<p style='color:red'>✗ Database connection failed: " . mysqli_connect_error() . "</p>";
    exit;
}

// Step 2: Check users table
echo "<h2>Step 2: Users Table Check</h2>";
$table_check = $conn->query("SHOW TABLES LIKE 'users'");
if ($table_check && $table_check->num_rows > 0) {
    echo "<p style='color:green'>✓ Users table exists</p>";
    
    // Check table structure
    $structure = $conn->query("DESCRIBE users");
    if ($structure) {
        echo "<p>Table structure:</p>";
        echo "<ul>";
        while ($field = $structure->fetch_assoc()) {
            echo "<li>" . $field['Field'] . " - " . $field['Type'] . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color:red'>✗ Users table does not exist</p>";
    
    // Create users table
    $create_table = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'staff', 'citizen') DEFAULT 'citizen',
        department_id INT NULL,
        phone VARCHAR(20) NULL,
        address TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table)) {
        echo "<p style='color:green'>✓ Users table created successfully</p>";
    } else {
        echo "<p style='color:red'>✗ Error creating users table: " . $conn->error . "</p>";
        exit;
    }
}

// Step 3: Create/Update admin user
echo "<h2>Step 3: Admin User Setup</h2>";

// First, check if admin exists
$admin_check = $conn->query("SELECT * FROM users WHERE username = 'admin'");
if ($admin_check && $admin_check->num_rows > 0) {
    $admin = $admin_check->fetch_assoc();
    echo "<p>Existing admin user found (ID: {$admin['id']})</p>";
    
    // Delete existing admin
    if ($conn->query("DELETE FROM users WHERE username = 'admin'")) {
        echo "<p style='color:green'>✓ Existing admin user deleted</p>";
    } else {
        echo "<p style='color:red'>✗ Error deleting admin user: " . $conn->error . "</p>";
    }
}

// Create new admin user with a simple password hash
$plain_password = 'admin123';
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "<p>Password details:</p>";
echo "<ul>";
echo "<li>Plain password: $plain_password</li>";
echo "<li>Hashed password: $hashed_password</li>";
echo "<li>Hash algorithm: " . PASSWORD_DEFAULT . "</li>";
echo "</ul>";

// Insert new admin
$insert_admin = "INSERT INTO users (username, password, email, full_name, role) 
                VALUES ('admin', '$hashed_password', 'admin@example.com', 'Administrator', 'admin')";

if ($conn->query($insert_admin)) {
    echo "<p style='color:green'>✓ New admin user created successfully</p>";
    
    // Get the new admin ID
    $new_admin = $conn->query("SELECT * FROM users WHERE username = 'admin'");
    if ($new_admin && $new_admin->num_rows > 0) {
        $admin_data = $new_admin->fetch_assoc();
        echo "<p>New admin ID: {$admin_data['id']}</p>";
    }
} else {
    echo "<p style='color:red'>✗ Error creating admin user: " . $conn->error . "</p>";
}

// Step 4: Test password verification
echo "<h2>Step 4: Password Verification Test</h2>";

// Get the admin user again
$admin_query = $conn->query("SELECT * FROM users WHERE username = 'admin'");
if ($admin_query && $admin_query->num_rows > 0) {
    $admin_user = $admin_query->fetch_assoc();
    $stored_hash = $admin_user['password'];
    
    echo "<p>Stored hash: $stored_hash</p>";
    
    // Test verification
    if (password_verify($plain_password, $stored_hash)) {
        echo "<p style='color:green'>✓ Password verification successful!</p>";
    } else {
        echo "<p style='color:red'>✗ Password verification failed!</p>";
        
        // Try with different algorithms
        echo "<p>Trying different verification methods:</p>";
        echo "<ul>";
        echo "<li>MD5: " . (md5($plain_password) == $stored_hash ? "Match" : "No match") . "</li>";
        echo "<li>SHA1: " . (sha1($plain_password) == $stored_hash ? "Match" : "No match") . "</li>";
        echo "<li>Simple comparison: " . ($plain_password == $stored_hash ? "Match" : "No match") . "</li>";
        echo "</ul>";
    }
} else {
    echo "<p style='color:red'>✗ Could not retrieve admin user for verification</p>";
}

// Step 5: Modify login.php to add debugging
echo "<h2>Step 5: Login Script Modification</h2>";

// Path to login.php
$login_path = 'login.php';

// Check if file exists
if (file_exists($login_path)) {
    // Read the file
    $login_content = file_get_contents($login_path);
    
    // Add debugging code
    $debug_code = "
    // Debug information for password verification
    if (isset(\$password) && isset(\$user['password'])) {
        error_log('Login attempt - Username: ' . \$username);
        error_log('Provided password: ' . \$password);
        error_log('Stored hash: ' . \$user['password']);
        error_log('password_verify result: ' . (password_verify(\$password, \$user['password']) ? 'true' : 'false'));
    }
    ";
    
    // Find the position to insert the debug code (before password verification)
    $position = strpos($login_content, "// Verify password");
    
    if ($position !== false) {
        // Insert the debug code
        $new_content = substr($login_content, 0, $position) . $debug_code . substr($login_content, $position);
        
        // Write the modified file
        if (file_put_contents($login_path, $new_content)) {
            echo "<p style='color:green'>✓ Login script modified with debugging code</p>";
        } else {
            echo "<p style='color:red'>✗ Failed to modify login script</p>";
        }
    } else {
        echo "<p style='color:red'>✗ Could not find the right position to insert debugging code</p>";
    }
} else {
    echo "<p style='color:red'>✗ Login script not found at $login_path</p>";
}

// Close connection
$conn->close();

echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;'>";
echo "<h3>Next Steps</h3>";
echo "<p>The admin user has been recreated with the following credentials:</p>";
echo "<ul>";
echo "<li><strong>Username:</strong> admin</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";
echo "<p>Try logging in with these credentials. If you still have issues, check the server error log for debugging information.</p>";
echo "<a href='login.php' style='display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
echo "</div>";
?>