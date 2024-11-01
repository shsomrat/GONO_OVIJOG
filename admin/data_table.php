<?php
// Include database configuration
include './config.php';

// Create the users table if it doesn't exist
$sqlusers = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role ENUM('admin', 'staff', 'viewer') NOT NULL,
  details TEXT,
  address VARCHAR(255),
  occupation VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute the CREATE TABLE query for users
if ($conn->query($sqlusers) === TRUE) {
  // Insert a default user if not already exists
  $checkUser = "SELECT * FROM users WHERE email = 'shsomrat@gmail.com'";
  $result = $conn->query($checkUser);

  if ($result->num_rows === 0) {
    $insertUser = "INSERT INTO users (full_name, email, password, phone, role, details, address, occupation) VALUES (
      'Sajjat Hossain Somrat',
      'shsomrat@gmail.com',
      '11211',
      '01767955086',
      'admin',
      'main admin',
      'Bhedorgonj, Shariatpur',
      'Programmer'
    )";

    if ($conn->query($insertUser) !== TRUE) {
      echo "Error inserting default user: " . $conn->error;
    }
  }
} else {
  echo "Error creating table: " . $conn->error;
}

// Create the departments table if it doesn't exist
$sqldepartments = "CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  thumbnail VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute the CREATE TABLE query for departments
if ($conn->query($sqldepartments) !== TRUE) {
  echo "Error creating departments table: " . $conn->error;
}

// Create the complaints table if it doesn't exist
$sqlComplaints = "CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    department INT,
    filled_by INT,
    status ENUM('approved', 'pending', 'In Progress', 'Under Review', 'Resolved') DEFAULT 'pending',
    victim_name VARCHAR(255) NOT NULL,
    victim_email VARCHAR(255) NOT NULL,
    victim_phone VARCHAR(20) NOT NULL,
    attached_doc VARCHAR(255),
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department) REFERENCES departments(id),
    FOREIGN KEY (filled_by) REFERENCES users(id)
)";

// Execute the CREATE TABLE query for complaints
if ($conn->query($sqlComplaints) !== TRUE) {
    die("Error creating complaints table: " . $conn->error);
}

// Create the complaint_update table with the attached_doc field
$sqlUpdate = "CREATE TABLE IF NOT EXISTS complaint_update (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    handler_id INT NOT NULL,
    complaint_title VARCHAR(255),
    department INT,
    complaint_description TEXT,
    complaint_created_at TIMESTAMP,
    status ENUM('Approved', 'In Progress', 'Under Review', 'Resolved', 'Rejected') NOT NULL,
    heading VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    attached_doc VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    FOREIGN KEY (department) REFERENCES departments(id),
    FOREIGN KEY (handler_id) REFERENCES users(id)
)";

// Execute the CREATE TABLE query for complaint_update
if ($conn->query($sqlUpdate) !== TRUE) {
    echo "Error creating complaint_update table: " . $conn->error;
}

?>
