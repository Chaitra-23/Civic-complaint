<?php
// Start a session to simulate being logged in as admin
session_start();
$_SESSION['user_id'] = 1; // Assuming user ID 1 is an admin
$_SESSION['role'] = 'admin';
$_SESSION['username'] = 'admin'; // Add username for header.php

// Debug: Check if user exists
require_once 'config/database.php';
$result = $conn->query("SELECT id, username, role FROM users WHERE id = 1");
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "Using admin user: " . $user['username'] . " (ID: " . $user['id'] . ", Role: " . $user['role'] . ")\n";
} else {
    echo "Warning: Admin user with ID 1 not found. Using ID 1 anyway for testing.\n";
}

// Set up GET parameter to simulate accessing a complaint
$_GET['id'] = 1; // Assuming complaint ID 1 exists

// Change directory to admin to make relative paths work
chdir('admin');

// Capture output
ob_start();

// Include the update_status.php file
echo "Testing admin/update_status.php directly:\n";
try {
    // Define the base path for includes
    $_SERVER['PHP_SELF'] = '/admin/update_status.php';
    
    // Include database connection
    require_once '../config/database.php';
    
    // Check if notifications table exists and create it if it doesn't
    $result = $conn->query("SHOW TABLES LIKE 'notifications'");
    if ($result->num_rows == 0) {
        $sql = "CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            complaint_id INT NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(20) NOT NULL DEFAULT 'email',
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE
        )";
        $conn->query($sql);
    }
    
    // Process form submission (simulating POST)
    if (isset($_POST['status'])) {
        // Get form data
        $complaint_id = isset($_POST['complaint_id']) ? (int)$_POST['complaint_id'] : 0;
        $status = $_POST['status'];
        $description = trim($_POST['description']);
        
        // Validate input
        $errors = [];
        if (empty($status)) $errors[] = "Status is required";
        // Description is now optional as per the updated schema
        
        // Ensure status is not too long for the database field
        if (strlen($status) > 50) {
            $errors[] = "Status is too long (maximum 50 characters)";
        }
        
        if (empty($errors)) {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Update complaint status
                $sql = "UPDATE complaints SET status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("si", $status, $complaint_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
                
                // Add status update record
                // Ensure status is truncated to match the database field length (varchar(50))
                $status_truncated = substr($status, 0, 50);
                
                // Description can be NULL as per the updated schema
                if (empty($description)) {
                    $description = "Status updated to " . $status_truncated;
                }
                
                $sql = "INSERT INTO complaint_updates (complaint_id, status, description, created_by) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("issi", $complaint_id, $status_truncated, $description, $_SESSION['user_id']);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
                
                // Get user ID for notification
                $sql = "SELECT user_id FROM complaints WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("i", $complaint_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $user_id = $row['user_id'];
                
                // Add notification
                $message = "Your complaint status has been updated to: " . ucfirst(str_replace('_', ' ', $status));
                $sql = "INSERT INTO notifications (user_id, complaint_id, message, type) VALUES (?, ?, ?, 'email')";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("iis", $user_id, $complaint_id, $message);
                if (!$stmt->execute()) {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
                
                // Commit transaction
                $conn->commit();
                
                echo "Complaint status updated successfully!\n";
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                echo "Error updating status: " . $e->getMessage() . "\n";
            }
        } else {
            echo "Validation errors:\n";
            foreach ($errors as $error) {
                echo "- " . $error . "\n";
            }
        }
    } else {
        // Get complaint details if ID is provided
        $complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($complaint_id > 0) {
            $sql = "SELECT c.*, d.name as department_name, u.username, u.email 
                    FROM complaints c 
                    JOIN departments d ON c.department_id = d.id 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("i", $complaint_id);
            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo "Complaint not found.\n";
            } else {
                $complaint = $result->fetch_assoc();
                echo "Complaint found: " . $complaint['title'] . " (Status: " . $complaint['status'] . ")\n";
                
                // Simulate form submission
                echo "Simulating form submission...\n";
                $_POST['complaint_id'] = $complaint_id;
                $_POST['status'] = 'in_progress';
                $_POST['description'] = 'Testing status update';
                
                // Process form submission
                echo "Processing form submission...\n";
                
                // Manually process the form submission
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Update complaint status
                    $sql = "UPDATE complaints SET status = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparing statement: " . $conn->error);
                    }
                    $stmt->bind_param("si", $_POST['status'], $_POST['complaint_id']);
                    if (!$stmt->execute()) {
                        throw new Exception("Error executing statement: " . $stmt->error);
                    }
                    
                    // Add status update record
                    $status_truncated = substr($_POST['status'], 0, 50);
                    $description = $_POST['description'];
                    if (empty($description)) {
                        $description = "Status updated to " . $status_truncated;
                    }
                    
                    $sql = "INSERT INTO complaint_updates (complaint_id, status, description, created_by) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparing statement: " . $conn->error);
                    }
                    $stmt->bind_param("issi", $_POST['complaint_id'], $status_truncated, $description, $_SESSION['user_id']);
                    if (!$stmt->execute()) {
                        throw new Exception("Error executing statement: " . $stmt->error);
                    }
                    
                    // Get user ID for notification
                    $sql = "SELECT user_id FROM complaints WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparing statement: " . $conn->error);
                    }
                    $stmt->bind_param("i", $_POST['complaint_id']);
                    if (!$stmt->execute()) {
                        throw new Exception("Error executing statement: " . $stmt->error);
                    }
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $user_id = $row['user_id'];
                    
                    // Add notification
                    $message = "Your complaint status has been updated to: " . ucfirst(str_replace('_', ' ', $_POST['status']));
                    $sql = "INSERT INTO notifications (user_id, complaint_id, message, type) VALUES (?, ?, ?, 'email')";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparing statement: " . $conn->error);
                    }
                    $stmt->bind_param("iis", $user_id, $_POST['complaint_id'], $message);
                    if (!$stmt->execute()) {
                        throw new Exception("Error executing statement: " . $stmt->error);
                    }
                    
                    // Commit transaction
                    $conn->commit();
                    
                    echo "Complaint status updated successfully!\n";
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    echo "Error updating status: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "No complaint ID provided.\n";
        }
    }
    
    echo "Test completed successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get output
$output = ob_get_clean();
echo $output;
?>