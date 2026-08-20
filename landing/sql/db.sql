CREATE DATABASE IF NOT EXISTS bhrsagq_db;

USE bhrsagq_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    dob DATE NOT NULL,
    contact VARCHAR(15) NOT NULL,
    civil_status ENUM('Single', 'Married', 'Widowed', 'Divorced') NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    address TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    resident_id VARCHAR(20) NOT NULL UNIQUE,
    role ENUM('Resident', 'Admin') NOT NULL DEFAULT 'Resident', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE admin_tbl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile VARCHAR(255) DEFAULT NULL,  
    email VARCHAR(255) NOT NULL UNIQUE,  
    password VARCHAR(255) NOT NULL,  
    fullname VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'Admin' 
);
INSERT INTO admin_tbl (profile, email, password, fullname)  -- pass: 1234
VALUES ('profile_picture.jpg', 'admin@gmail.com', '$2y$10$PbPLyuXdsW715wkgYvk8Y.uO3trnb2JkQk5BjZ6rUIZJ1E6y/lewi', 'Admin User');


CREATE TABLE `medical_transactions_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_name` varchar(255) NOT NULL,
  `consultation_type` varchar(255) NOT NULL,
  `appointment_date` varchar(255) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `medical_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
