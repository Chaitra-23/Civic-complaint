<?php
require_once 'includes/header.php';

// Check if complaint_images table exists and create it if it doesn't
$result = $conn->query("SHOW TABLES LIKE 'complaint_images'");
if ($result->num_rows == 0) {
    $sql = "CREATE TABLE IF NOT EXISTS complaint_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        complaint_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE
    )";
    $conn->query($sql);
}

// Check if priority column exists in complaints table and add it if it doesn't
$result = $conn->query("SHOW COLUMNS FROM complaints LIKE 'priority'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE complaints ADD COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER location";
    $conn->query($sql);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $department_id = (int)$_POST['department_id'];
    $priority = $_POST['priority'];
    
    // Validate input
    $errors = [];
    if (empty($title)) $errors[] = "Title is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($location)) $errors[] = "Location is required";
    
    if (empty($errors)) {
        // Try a direct query instead of prepared statement
        $sql = "INSERT INTO complaints (user_id, department_id, title, description, location, priority) 
                VALUES ('" . $conn->real_escape_string($_SESSION['user_id']) . "', 
                        '" . $conn->real_escape_string($department_id) . "', 
                        '" . $conn->real_escape_string($title) . "', 
                        '" . $conn->real_escape_string($description) . "', 
                        '" . $conn->real_escape_string($location) . "', 
                        '" . $conn->real_escape_string($priority) . "')";
        
        if ($conn->query($sql)) {
            $complaint_id = $conn->insert_id;
            
            // Handle image upload
            if (!empty($_FILES['images']['name'][0])) {
                $upload_dir = 'uploads/complaints/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $file_name = $_FILES['images']['name'][$key];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $target_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $img_sql = "INSERT INTO complaint_images (complaint_id, image_path) 
                                   VALUES ('" . $conn->real_escape_string($complaint_id) . "', 
                                           '" . $conn->real_escape_string($target_path) . "')";
                        $conn->query($img_sql);
                    }
                }
            }
            
            $_SESSION['message'] = "Complaint submitted successfully!";
            $_SESSION['message_type'] = "success";
            header('Location: my_complaints.php');
            exit();
        } else {
            $errors[] = "Error submitting complaint: " . $conn->error . " (SQL: $sql)";
        }
    }
}

// Get departments for dropdown
$departments = [];
$sql = "SELECT id, name FROM departments ORDER BY name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Submit New Complaint</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="submit_complaint.php" method="post" enctype="multipart/form-data" id="complaintForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php 
                                echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-select" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php 
                                        echo (isset($_POST['department_id']) && $_POST['department_id'] == $dept['id']) ? 'selected' : ''; 
                                    ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required 
                                   value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
                            <div id="locationPicker" style="height: 300px; width: 100%; margin-top: 10px;"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Upload Images (Optional)</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <small class="text-muted">You can upload multiple images. Maximum file size: 5MB each.</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Complaint
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Maps API -->
<!--iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0968173775!2d-122.39568308439042!3d37.78289997975903!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80858062c2c9e2b7%3A0x4b4b7d19786e3fbd!2sSan%20Francisco%2C%20CA%2094105!5e0!3m2!1sen!2sus!4v1625012345678!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe-->
<script>
// Initialize a simple location picker without Google Maps
function initLocationPicker() {
    // Create a simple text-based location picker
    const locationInput = document.getElementById('location');
    const locationPicker = document.getElementById('locationPicker');
    
    // Replace the map with instructions
    locationPicker.innerHTML = `
        <div class="alert alert-info">
            <p><strong>Location Instructions:</strong></p>
            <p>Please enter a detailed address or description of the location in the field above.</p>
            <p>Example: "123 Main Street, City" or "Near City Park, opposite to Gas Station"</p>
        </div>
    `;
    locationPicker.style.height = 'auto';
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', initLocationPicker);
</script>

<?php require_once 'includes/footer.php'; ?> 