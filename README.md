# Civic Complaints System

A web-based system for citizens to submit and track civic complaints for proactive maintenance.

## Features

- **User Registration and Login**: Allows citizens to create accounts and log in to raise complaints.
- **Complaint Submission**: Enables users to submit complaints with detailed descriptions and images.
- **Complaint Tracking**: Provides real-time updates on the status of submitted complaints.
- **Admin Dashboard**: Allows administrators to view, categorize, and assign complaints to appropriate departments.
- **Advanced Analytics**: Interactive charts and visualizations for complaint data analysis.
- **Department Performance Metrics**: Track resolution rates and times for different departments.
- **Reporting and Analytics**: Generates reports and analyzes data to identify common issues and improve service delivery.
- **Notification Module**: Notifies users about complaint progress via the system.
- **Dark Mode Support**: Toggle between light and dark themes for better user experience.
- **Mobile Responsive Design**: Optimized for all device sizes from smartphones to desktops.

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5, Chart.js
- **Backend**: PHP
- **Database**: MySQL
- **Server**: Apache
- **Additional Libraries**: Font Awesome for icons

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server

### Installation

1. **Set up the web server**:
   - Install MAMP (macOS), WAMP (Windows), or LAMP (Linux) for an all-in-one solution
   - Or set up Apache, PHP, and MySQL individually

2. **Configure the database**:
   - Create a MySQL database named `civic_complaints`
   - Update database connection details in `config/database.php` if needed:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'root');
     define('DB_PASSWORD', '');
     define('DB_NAME', 'civic_complaints');
     ```

3. **Set up the project**:
   - Clone or download the project to your web server's document root
   - Navigate to `http://localhost/Complaints/setup.php` to initialize the database with tables and sample data
   - After setup, you can log in with the following credentials:
     - Admin: Username: `admin`, Password: `admin123`
     - User: Username: `user`, Password: `user123`

## Usage

1. **User Functions**:
   - Register a new account or log in
   - Submit new complaints with details and optional images
   - Track the status of submitted complaints
   - Receive notifications about complaint updates
   - Update profile information
   - Toggle between light and dark mode for better viewing experience
   - Access the system from any device with responsive design

2. **Admin Functions**:
   - View all complaints in the admin dashboard
   - Filter complaints by status, department, and priority
   - Update complaint status and add status updates
   - View interactive statistics and reports with Chart.js visualizations
   - Access detailed analytics on the Analytics Dashboard
   - Monitor department performance metrics
   - Generate insights for proactive maintenance planning

## Project Structure

- `admin/`: Admin-specific pages and functionality
  - `dashboard.php`: Main admin dashboard
  - `analytics.php`: Advanced analytics and charts
  - `update_status.php`: Update complaint status
- `api/`: API endpoints for AJAX functionality
  - `dashboard_stats.php`: Provides data for dashboard charts
  - `department_performance.php`: Department performance metrics
- `assets/`: CSS, JavaScript, and image files
  - `css/`: Stylesheet files
    - `styles.css`: Main stylesheet
    - `mobile.css`: Mobile-specific styles
    - `dark-mode.css`: Dark mode styles
  - `js/`: JavaScript files
    - `main.js`: Main JavaScript functionality
    - `dashboard.js`: Dashboard charts and analytics
    - `dark-mode.js`: Dark mode toggle functionality
- `config/`: Configuration files
  - `database.php`: Database connection settings
- `database/`: Database schema and setup files
  - `schema.sql`: Database schema
- `includes/`: Reusable PHP components
  - `header.php`: Common header
  - `footer.php`: Common footer
- `uploads/`: Directory for uploaded images

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- Bootstrap for the responsive UI components
- Font Awesome for the icons
- Chart.js for interactive data visualizations
- MAMP/WAMP/LAMP for the development environment

## Future Enhancements

- **GIS Integration**: Map-based visualization of complaints
- **Mobile App**: Native mobile applications for iOS and Android
- **AI-Powered Categorization**: Automatic categorization of complaints using machine learning
- **Public API**: API for third-party integrations
- **Social Media Integration**: Share and report issues via social media platforms
- **Multi-language Support**: Internationalization for multiple languages