// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Image preview for complaint submission
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Real-time complaint status updates
function updateComplaintStatus(complaintId) {
    fetch(`api/get_complaint_status.php?id=${complaintId}`)
        .then(response => response.json())
        .then(data => {
            const statusElement = document.getElementById(`status-${complaintId}`);
            if (statusElement) {
                statusElement.className = `complaint-status status-${data.status}`;
                statusElement.textContent = data.status.replace('_', ' ');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Notification handling
function checkNotifications() {
    if (document.getElementById('notificationCount')) {
        fetch('api/check_notifications.php')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationCount');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error:', error));
    }
}

// Auto-refresh notifications every 30 seconds
setInterval(checkNotifications, 30000);

// Location picker for complaint submission
function initLocationPicker() {
    if (document.getElementById('locationPicker')) {
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
}

// Initialize components when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize location picker
    initLocationPicker();

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form.id)) {
                e.preventDefault();
            }
        });
    });

    // Image upload preview
    const imageInput = document.getElementById('complaintImage');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            previewImage(this);
        });
    }
    
    // Initialize counter animation for statistics
    initCounterAnimation();
    
    // Add active class to current nav item
    highlightCurrentNavItem();
});

// AJAX form submission
function submitFormAjax(formId, successCallback) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof successCallback === 'function') {
                successCallback(data);
            }
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the form');
    });
}

/**
 * Initialize counter animation for statistics
 */
function initCounterAnimation() {
    const counters = document.querySelectorAll('.counter');
    
    if (counters.length > 0) {
        const speed = 200;
        
        counters.forEach(counter => {
            // Store the original value
            const finalValue = parseInt(counter.innerText);
            // Reset to zero for animation
            counter.innerText = '0';
            
            const animate = () => {
                const value = finalValue;
                const data = +counter.innerText;
                
                const time = value / speed;
                
                if (data < value) {
                    counter.innerText = Math.ceil(data + time);
                    setTimeout(animate, 1);
                } else {
                    counter.innerText = value;
                }
            }
            
            animate();
        });
    }
}

/**
 * Add active class to current nav item
 */
function highlightCurrentNavItem() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        if (href === currentPage || 
            (currentPage === '' && href === 'index.php') ||
            (currentPage.includes('admin') && href.includes('admin'))) {
            link.classList.add('active');
        }
    });
}

/**
 * Format date to readable format
 * @param {string} dateString - Date string to format
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Format time to readable format
 * @param {string} dateString - Date string to format
 * @returns {string} Formatted time
 */
function formatTime(dateString) {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleTimeString(undefined, options);
}

/**
 * Format date and time to readable format
 * @param {string} dateString - Date string to format
 * @returns {string} Formatted date and time
 */
function formatDateTime(dateString) {
    return formatDate(dateString) + ' at ' + formatTime(dateString);
} 