<?php
require_once 'config/database.php';

class NotificationSystem {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Send notification to user
    public function sendNotification($user_id, $complaint_id, $message, $type = 'email') {
        // Insert notification into database
        $sql = "INSERT INTO notifications (user_id, complaint_id, message, type) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiss", $user_id, $complaint_id, $message, $type);
        
        if ($stmt->execute()) {
            $notification_id = $stmt->insert_id;
            
            // Get user details
            $sql = "SELECT email, phone FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            // Send notification based on type
            if ($type === 'email' && !empty($user['email'])) {
                $this->sendEmail($user['email'], $message);
            } elseif ($type === 'sms' && !empty($user['phone'])) {
                $this->sendSMS($user['phone'], $message);
            }
            
            // Update notification status
            $sql = "UPDATE notifications SET status = 'sent' WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $notification_id);
            $stmt->execute();
            
            return true;
        }
        
        return false;
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
        $sql = "SELECT n.*, c.title as complaint_title 
                FROM notifications n 
                JOIN complaints c ON n.complaint_id = c.id 
                WHERE n.user_id = ? 
                ORDER BY n.created_at DESC 
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        return $notifications;
    }
    
    // Mark notification as read
    public function markAsRead($notification_id) {
        $sql = "UPDATE notifications SET status = 'read' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $notification_id);
        return $stmt->execute();
    }
    
    // Get unread notification count
    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND status = 'sent'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'];
    }
}

// Initialize notification system
$notificationSystem = new NotificationSystem($conn);
?> 