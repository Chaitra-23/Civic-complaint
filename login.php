<?php
require_once 'includes/header.php';

// Initialize variables
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username)) {
        $errors[] = "Username or email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // Authenticate user
    if (empty($errors)) {
        // Check if username/email exists
        $sql = "SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Debug information for password verification
            if (isset($password) && isset($user['password'])) {
                error_log('Login attempt - Username: ' . $username);
                error_log('Provided password: ' . $password);
                error_log('Stored hash: ' . $user['password']);
                error_log('password_verify result: ' . (password_verify($password, $user['password']) ? 'true' : 'false'));
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Clear any existing session data
                session_unset();
                
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Debug information
                error_log("User logged in - ID: " . $user['id'] . ", Username: " . $user['username'] . ", Role: " . $user['role']);
                error_log("Session data: " . print_r($_SESSION, true));
                
                // Force session write
                session_write_close();
                
                // Redirect based on role - check for any variation of 'admin'
                $role = strtolower($user['role']);
                if ($role === 'admin' || $role === 'administrator') {
                    echo "<script>window.location.href = 'admin/dashboard.php';</script>";
                    exit();
                } else {
                    echo "<script>window.location.href = 'index.php';</script>";
                    exit();
                }
            } else {
                $errors[] = "Invalid password";
            }
        } else {
            $errors[] = "Username or email not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Civic Complaints System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="auth-logo text-center mb-4">
                        <h2 class="text-white">Civic Complaints System</h2>
                    </div>
                    
                    <div class="auth-card card">
                        <div class="row g-0">
                            <div class="col-md-6 d-none d-md-block">
                                <div class="auth-illustration p-4">
                                    <img src="assets/images/login-illustration.svg" alt="Login" onerror="this.src='https://cdn.pixabay.com/photo/2017/07/31/11/44/laptop-2557576_1280.jpg'; this.style.opacity='0.7';">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-header">
                                    <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Welcome Back</h4>
                                    <p class="text-white-50 mb-0">Sign in to your account</p>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_SESSION['message'])): ?>
                                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                                            <?php 
                                                echo $_SESSION['message']; 
                                                unset($_SESSION['message']);
                                                unset($_SESSION['message_type']);
                                            ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $error): ?>
                                                    <li><?php echo $error; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form action="login.php" method="post" class="auth-form">
                                        <div class="mb-4">
                                            <label for="username" class="form-label">Username or Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username or email" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between">
                                                <label for="password" class="form-label">Password</label>
                                                <a href="#" class="auth-link small">Forgot Password?</a>
                                            </div>
                                            <div class="input-group password-field-wrapper">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                                <span class="password-toggle" onclick="togglePassword('password')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-4 form-check">
                                            <input type="checkbox" class="form-check-input" id="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary auth-btn">
                                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <div class="auth-divider">
                                        <span>or sign in with</span>
                                    </div>
                                    
                                    <div class="social-login">
                                        <a href="#" class="social-btn google">
                                            <i class="fab fa-google"></i>
                                        </a>
                                        <a href="#" class="social-btn facebook">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        <a href="#" class="social-btn twitter">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <p class="mb-0">Don't have an account? <a href="register_new.php" class="auth-link">Register here</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.querySelector(`#${fieldId} + .password-toggle i`);
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>