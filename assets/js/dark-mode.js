/**
 * Civic Complaints System - Dark Mode Toggle
 * This file contains JavaScript for toggling dark mode
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check for saved dark mode preference or use system preference
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const storedTheme = localStorage.getItem('theme');
    
    if (storedTheme === 'dark' || (!storedTheme && prefersDarkScheme.matches)) {
        document.body.classList.add('dark-mode');
        document.body.classList.add('dark-mode-support');
        if (document.getElementById('darkModeToggle')) {
            document.getElementById('darkModeToggle').checked = true;
        }
    }
    
    // Add dark mode toggle to the navbar
    addDarkModeToggle();
    
    // Listen for changes in system preference
    prefersDarkScheme.addEventListener('change', function(e) {
        if (!localStorage.getItem('theme')) {
            if (e.matches) {
                document.body.classList.add('dark-mode');
                document.body.classList.add('dark-mode-support');
                if (document.getElementById('darkModeToggle')) {
                    document.getElementById('darkModeToggle').checked = true;
                }
            } else {
                document.body.classList.remove('dark-mode');
                document.body.classList.remove('dark-mode-support');
                if (document.getElementById('darkModeToggle')) {
                    document.getElementById('darkModeToggle').checked = false;
                }
            }
        }
    });
});

/**
 * Add dark mode toggle to the navbar
 */
function addDarkModeToggle() {
    // Create the toggle switch HTML
    const toggleHtml = `
        <li class="nav-item d-flex align-items-center ms-lg-3">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="darkModeToggle" role="switch">
                <label class="form-check-label text-light d-none d-sm-inline-block ms-2" for="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </label>
            </div>
        </li>
    `;
    
    // Find the navbar
    const navbarNav = document.querySelector('.navbar-nav:last-child');
    if (navbarNav) {
        // Insert the toggle before the first item
        navbarNav.insertAdjacentHTML('afterbegin', toggleHtml);
        
        // Add event listener to the toggle
        const toggle = document.getElementById('darkModeToggle');
        if (toggle) {
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    document.body.classList.add('dark-mode');
                    document.body.classList.add('dark-mode-support');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.body.classList.remove('dark-mode');
                    document.body.classList.remove('dark-mode-support');
                    localStorage.setItem('theme', 'light');
                }
            });
        }
    }
}