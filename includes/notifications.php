<?php
require_once 'config/database.php';

class NotificationSystem {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Send notification to user
    public function sendNotification($user_id, $complaint_id, $message, $type = 'email') {
        try {
            // Insert notification into database
            $sql = "INSERT INTO notifications (user_id, complaint_id, message, type) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt === false) {
                error_log("Prepare failed in sendNotification (insert): " . $this->conn->error);
                return false;
            }
            
            $stmt->bind_param("iiss", $user_id, $complaint_id, $message, $type);
            
            if ($stmt->execute()) {
                $notification_id = $stmt->insert_id;
                $stmt->close();
                
                // Get user details
                $sql = "SELECT email, phone FROM users WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                
                if ($stmt === false) {
                    error_log("Prepare failed in sendNotification (select user): " . $this->conn->error);
                    return false;
                }
                
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
                
                // Send notification based on type
                if ($type === 'email' && !empty($user['email'])) {
                    $this->sendEmail($user['email'], $message);
                } elseif ($type === 'sms' && !empty($user['phone'])) {
                    $this->sendSMS($user['phone'], $message);
                }
                
                // Update notification status
                $sql = "UPDATE notifications SET status = 'sent' WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                
                if ($stmt === false) {
                    error_log("Prepare failed in sendNotification (update): " . $this->conn->error);
                    return false;
                }
                
                $stmt->bind_param("i", $notification_id);
                $result = $stmt->execute();
                $stmt->close();
                
                return true;
            } else {
                error_log("Execute failed in sendNotification: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in sendNotification: " . $e->getMessage());
            return false;
        }
    }
    
    // Send email notification
    private function sendEmail($to, $message) {
        $subject = "Civic Complaints System - Status Update";
        $headers = "From: noreply@civiccomplaints.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_content = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f8f9fa; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Civic Complaints System</h2>
                    </div>
                    <div class='content'>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                    <div class='footer'>
                        This is an automated message. Please do not reply to this email.
                    </div>
                </div>
            </body>
            </html>
        ";
        
        mail($to, $subject, $email_content, $headers);
    }
    
    // Send SMS notification
    private function sendSMS($phone, $message) {
        // Replace with your SMS gateway API
        $api_key = "YOUR_SMS_API_KEY";
        $api_url = "https://api.sms-gateway.com/send";
        
        $data = [
            'api_key' => $api_key,
            'to' => $phone,
            'message' => $message
        ];
        
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    // Get user's notifications
    public function getUserNotifications($user_id, $limit = 10) {
        // First, check if the tables exist
        $tables_exist = true;
        
        // Check if notifications table exists
        $result = $this->conn->query("SHOW TABLES LIKE 'notifications'");
        if ($result->num_rows == 0) {
            error_log("Notifications table does not exist");
            $tables_exist = false;
        }
        
        // Check if complaints table exists
        $result = $this->conn->query("SHOW TABLES LIKE 'complaints'");
        if ($result->num_rows == 0) {
            error_log("Complaints table does not exist");
            $tables_exist = false;
        }
        
        if (!$tables_exist) {
            return [];
        }
        
        // Try to get notifications with a JOIN to complaints
        try {
            $sql = "SELECT n.*, c.title as complaint_title 
                    FROM notifications n 
                    JOIN complaints c ON n.complaint_id = c.id 
                    WHERE n.user_id = ? 
                    ORDER BY n.created_at DESC 
                    LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt === false) {
                // Handle prepare error
                error_log("Prepare failed: " . $this->conn->error);
                
                // Try a simpler query without the JOIN as a fallback
                $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
                $stmt = $this->conn->prepare($sql);
                
                if ($stmt === false) {
                    error_log("Fallback prepare failed: " . $this->conn->error);
                    return [];
                }
            }
            
            $stmt->bind_param("ii", $user_id, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                // If we're using the fallback query, set a default complaint_title
                if (!isset($row['complaint_title'])) {
                    $row['complaint_title'] = 'Complaint #' . $row['complaint_id'];
                }
                $notifications[] = $row;
            }
            
            $stmt->close();
            return $notifications;
        } catch (Exception $e) {
            error_log("Exception in getUserNotifications: " . $e->getMessage());
            return [];
        }
    }
    
    // Mark notification as read
    public function markAsRead($notification_id) {
        $sql = "UPDATE notifications SET status = 'read' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed in markAsRead: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $notification_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    // Get unread notification count
    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'sent'";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed in getUnreadCount: " . $this->conn->error);
            return 0;
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $stmt->close();
        return $row['count'];
    }
}

// Initialize notification system
$notificationSystem = new NotificationSystem($conn);
?> 