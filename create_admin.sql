USE civic_complaints;

-- Create a test admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$8tPjdcZCL/3LQwX.GZ9Jn.XyIEJBG6EO0nY1QGiSgMdNJGFn9YyLG', 'Admin User', 'admin');

-- Create a test department
INSERT INTO departments (name, description)
VALUES ('Public Works', 'Responsible for infrastructure maintenance and public facilities');