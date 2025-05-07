<?php
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Civic Complaints System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
    <style>
        .terms-container {
            padding: 40px 0;
        }
        .terms-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .terms-header {
            background-color: #3498db;
            color: white;
            padding: 20px;
        }
        .terms-body {
            padding: 30px;
        }
        .terms-section {
            margin-bottom: 30px;
        }
        .terms-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .back-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container terms-container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="terms-card card">
                    <div class="terms-header">
                        <h2><i class="fas fa-gavel me-2"></i>Terms of Service</h2>
                        <p class="mb-0">Last Updated: <?php echo date("F d, Y"); ?></p>
                    </div>
                    <div class="terms-body">
                        <div class="terms-section">
                            <h3>1. Acceptance of Terms</h3>
                            <p>Welcome to the Civic Complaints System. By accessing or using our service, you agree to be bound by these Terms of Service. If you disagree with any part of the terms, you may not access the service.</p>
                        </div>
                        
                        <div class="terms-section">
                            <h3>2. Description of Service</h3>
                            <p>The Civic Complaints System provides a platform for citizens to report civic issues, track their status, and communicate with local authorities. The service is provided "as is" and "as available" without warranties of any kind.</p>
                        </div>
                        
                        <div class="terms-section">
                            <h3>3. User Accounts</h3>
                            <p>To use certain features of the service, you must register for an account. You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account. You agree to:</p>
                            <ul>
                                <li>Provide accurate and complete information when creating your account</li>
                                <li>Update your information to keep it accurate and current</li>
                                <li>Protect your account password and notify us of any unauthorized use</li>
                                <li>Take responsibility for all activities that occur under your account</li>
                            </ul>
                        </div>
                        
                        <div class="terms-section">
                            <h3>4. User Conduct</h3>
                            <p>When using our service, you agree not to:</p>
                            <ul>
                                <li>Submit false or misleading complaints</li>
                                <li>Harass, abuse, or harm another person</li>
                                <li>Use the service for any illegal purpose</li>
                                <li>Interfere with or disrupt the service</li>
                                <li>Attempt to access areas or features you are not authorized to access</li>
                                <li>Post content that is offensive, defamatory, or violates others' rights</li>
                            </ul>
                        </div>
                        
                        <div class="terms-section">
                            <h3>5. Content Submission</h3>
                            <p>By submitting content to our service (including complaints, comments, and images), you grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, modify, and display the content in connection with the service. You represent and warrant that you own or have the necessary rights to the content you submit.</p>
                        </div>
                        
                        <div class="terms-section">
                            <h3>6. Termination</h3>
                            <p>We reserve the right to terminate or suspend your account and access to the service at our sole discretion, without notice, for conduct that we believe violates these Terms of Service or is harmful to other users, us, or third parties, or for any other reason.</p>
                        </div>
                        
                        <div class="terms-section">
                            <h3>7. Changes to Terms</h3>
                            <p>We reserve the right to modify or replace these Terms of Service at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>
                        </div>
                        
                        <div class="terms-section">
                            <h3>8. Contact Information</h3>
                            <p>If you have any questions about these Terms of Service, please contact us at support@civiccomplaints.example.com.</p>
                        </div>
                        
                        <div class="back-link text-center">
                            <a href="register_new.php" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Back to Previous Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>