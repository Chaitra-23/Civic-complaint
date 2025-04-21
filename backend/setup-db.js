const mysql = require('mysql2'); // Import MySQL client
const fs = require('fs');
const path = require('path');
require('dotenv').config();

// Create a connection to the MySQL database
const connection = mysql.createConnection({
  host: process.env.MYSQL_HOST,
  user: process.env.MYSQL_USER,
  password: process.env.MYSQL_PASSWORD,
  database: process.env.MYSQL_DATABASE,
});

// Connect to the MySQL database
connection.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err);
    return;
  }
  console.log('Connected to the MySQL database.');

  // Here you can add your table creation logic
  const createComplaintsTable = `
    CREATE TABLE IF NOT EXISTS complaints (
      id INT AUTO_INCREMENT PRIMARY KEY,
      title VARCHAR(255) NOT NULL,
      description TEXT NOT NULL,
      status ENUM('open', 'in_progress', 'resolved') DEFAULT 'open',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
  `;

  connection.query(createComplaintsTable, (err, results) => {
    if (err) {
      console.error('Error creating table:', err);
    } else {
      console.log('Complaints table created or already exists.');
    }

    // Close the connection after table creation
    connection.end(); // Close the connection
  });
}); 