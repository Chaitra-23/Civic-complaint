USE civic_complaints;

-- Create a test complaint
INSERT INTO complaints (user_id, department_id, title, description, location, status, priority)
VALUES (1, 1, 'Test Complaint', 'This is a test complaint for testing the update status functionality', 'Test Location', 'pending', 'medium');