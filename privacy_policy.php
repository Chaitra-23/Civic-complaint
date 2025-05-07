<?php
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Civic Complaints System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
    <style>
        .privacy-container {
            padding: 40px 0;
        }
        .privacy-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .privacy-header {
            background-color: #2ecc71;
            color: white;
            padding: 20px;
        }
        .privacy-body {
            padding: 30px;
        }
        .privacy-section {
            margin-bottom: 30px;
        }
        .privacy-section h3 {
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
    <div class="container privacy-container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="privacy-card card">
                    <div class="privacy-header">
                        <h2><i class="fas fa-shield-alt me-2"></i>Privacy Policy</h2>
                        <p class="mb-0">Last Updated: <?php echo date("F d, Y"); ?></p>
                    </div>
                    <div class="privacy-body">
                        <div class="privacy-section">
                            <h3>1. Introduction</h3>
                            <p>Welcome to the Civic Complaints System. We respect your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our service.</p>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>2. Information We Collect</h3>
                            <p>We collect several types of information from and about users of our service, including:</p>
                            <ul>
                                <li><strong>Personal Information:</strong> Name, email address, phone number, and other identifiers you provide during registration or when submitting complaints.</li>
                                <li><strong>Location Data:</strong> Geographic information related to reported issues, which may include precise location data if you choose to share it.</li>
                                <li><strong>Content:</strong> Information contained in your complaints, comments, and any media files you upload.</li>
                                <li><strong>Usage Data:</strong> Information about how you access and use our service, including your IP address, browser type, device information, and pages visited.</li>
                            </ul>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>3. How We Use Your Information</h3>
                            <p>We use the information we collect for various purposes, including to:</p>
                            <ul>
                                <li>Provide, maintain, and improve our service</li>
                                <li>Process and manage your complaints</li>
                                <li>Communicate with you about your account, complaints, or updates to our service</li>
                                <li>Analyze usage patterns to enhance user experience</li>
                                <li>Protect against fraudulent or unauthorized activity</li>
                                <li>Comply with legal obligations</li>
                            </ul>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>4. Information Sharing</h3>
                            <p>We may share your information in the following circumstances:</p>
                            <ul>
                                <li><strong>With Government Agencies:</strong> To process and resolve your complaints with the appropriate authorities.</li>
                                <li><strong>With Service Providers:</strong> Third-party vendors who perform services on our behalf, such as hosting, data analysis, and customer service.</li>
                                <li><strong>For Legal Reasons:</strong> To comply with applicable laws, regulations, legal processes, or governmental requests.</li>
                                <li><strong>With Your Consent:</strong> In any other circumstances where we have your explicit consent.</li>
                            </ul>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>5. Data Security</h3>
                            <p>We implement appropriate technical and organizational measures to protect your personal information from unauthorized access, disclosure, alteration, or destruction. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>6. Your Rights</h3>
                            <p>Depending on your location, you may have certain rights regarding your personal information, including:</p>
                            <ul>
                                <li>The right to access your personal information</li>
                                <li>The right to correct inaccurate or incomplete information</li>
                                <li>The right to delete your personal information</li>
                                <li>The right to restrict or object to processing</li>
                                <li>The right to data portability</li>
                            </ul>
                            <p>To exercise these rights, please contact us using the information provided in the "Contact Us" section.</p>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>7. Children's Privacy</h3>
                            <p>Our service is not intended for individuals under the age of 16. We do not knowingly collect personal information from children. If you are a parent or guardian and believe your child has provided us with personal information, please contact us.</p>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>8. Changes to This Privacy Policy</h3>
                            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes.</p>
                        </div>
                        
                        <div class="privacy-section">
                            <h3>9. Contact Us</h3>
                            <p>If you have any questions about this Privacy Policy, please contact us at privacy@civiccomplaints.example.com.</p>
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