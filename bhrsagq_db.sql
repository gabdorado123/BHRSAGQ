

CREATE TABLE `admin_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_tbl` VALUES ('1', 'uploads/PROFILE-PICTURE-FOR-FACEBOOK.jpg', 'admin', '$2y$10$PbPLyuXdsW715wkgYvk8Y.uO3trnb2JkQk5BjZ6rUIZJ1E6y/lewi', 'Admin User', 'Admin');


CREATE TABLE `appointment_slots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slot_time` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `appointment_slots` VALUES ('1', '4', '2025-04-30 13:38:25');


CREATE TABLE `cancel_appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_number` varchar(50) DEFAULT NULL,
  `resident_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `consultation_type` varchar(100) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `cancel_appointments` VALUES ('2', '1', 'RES-2025-009', 'Elena', 'Bautista', 'M', 'Dr. Maria Santos - Cardiologist', 'Hypertension', '2025-05-05', '165', '66', '2025-05-05 06:33:29');
INSERT INTO `cancel_appointments` VALUES ('3', '2', 'RES-2025-003', 'Maria', 'Santos', 'M', 'Dr. Stephen Strange - Cardiologist', 'Cardiology', '2025-05-05', '170', '67', '2025-05-05 07:47:58');
INSERT INTO `cancel_appointments` VALUES ('4', '2', 'RES-2025-012', 'Roberto', 'Iglesias', 'S', 'Dr. Robert Miranda - Cardiologist', 'Hypertension,Cardiology', '2025-05-06', '157', '80', '2025-05-06 16:37:16');
INSERT INTO `cancel_appointments` VALUES ('5', '2', 'RES-2025-012	', 'Roberto', 'Iglesias', 'S', 'Dr. Leon Lee - Cardiogolist', 'Cardiology', '2025-05-06', '155', '86', '2025-05-06 16:38:31');
INSERT INTO `cancel_appointments` VALUES ('6', '3', 'RES-2025-005', 'Ana', 'Lopez', 'L', 'Dr. Stephen Strange - Cardiologist', 'Cardiology', '2025-05-06', '167', '66', '2025-05-06 16:40:12');
INSERT INTO `cancel_appointments` VALUES ('7', '3', 'RES-2025-004', 'Jose', 'Rivera', 'M', 'Dr. Maria Santos - Cardiologist', 'Hypertension,Cardiology', '2025-05-06', '160', '55', '2025-05-06 16:43:48');
INSERT INTO `cancel_appointments` VALUES ('8', '2', 'RES-2025-012', 'Roberto', 'Iglesias', 'S', 'Dr. Robert Miranda - Cardiologist', 'Hypertension', '2025-05-06', '165', '81', '2025-05-06 16:44:14');
INSERT INTO `cancel_appointments` VALUES ('9', '2', 'RES-2025-007', 'Luciana', 'Reyes', 'M', 'Dr. Robert Miranda - Cardiologist', 'Cardiology', '2025-05-06', '165', '55', '2025-05-06 16:49:28');
INSERT INTO `cancel_appointments` VALUES ('10', '1', 'RES-2025-004', 'Jose', 'Rivera', 'M', 'Dr. Alan Reyes - Cardiologist', 'Hypertension,Cardiology', '2025-05-06', '167', '55', '2025-05-06 17:06:55');
INSERT INTO `cancel_appointments` VALUES ('11', '3', 'RES-2025-009', 'Elena', 'Bautista', 'M', 'Dr. Robert Miranda - Cardiologist', 'Cardiology', '2025-05-06', '155', '60', '2025-05-06 17:11:28');
INSERT INTO `cancel_appointments` VALUES ('12', '4', 'RES-2025-010', 'Teresa', 'Diaz', 'T', 'Dr. Steve Rogers - Cardiologist', 'Hypertension', '2025-05-08', '155', '66', '2025-05-08 22:24:51');


CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `doctors` VALUES ('1', 'Mr. Lee', 'Cardiologist');
INSERT INTO `doctors` VALUES ('2', 'Dr. Mark Smith', 'Psychiatrist');
INSERT INTO `doctors` VALUES ('3', 'Mr. Junior', 'Pediatrics');
INSERT INTO `doctors` VALUES ('4', 'Dr. Alan Reyes', 'Cardiologist');
INSERT INTO `doctors` VALUES ('5', 'Dr. Maria Santos', 'Cardiologist');
INSERT INTO `doctors` VALUES ('7', 'Dr. Stephen Strange', 'Cardiologist');
INSERT INTO `doctors` VALUES ('10', 'Dr. Tony Stark', 'Pediatrics');
INSERT INTO `doctors` VALUES ('11', 'Dr. Robert Miranda', 'Cardiologist');
INSERT INTO `doctors` VALUES ('12', 'Dr. Robert Miranda', 'Cardiologist');
INSERT INTO `doctors` VALUES ('13', 'Dr. Leon Lee', 'Cardiogolist');
INSERT INTO `doctors` VALUES ('14', 'Dr. Steve Rogers', 'Cardiologist');


CREATE TABLE `medical_transactions_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` varchar(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_name` varchar(255) NOT NULL,
  `consultation_type` varchar(255) NOT NULL,
  `appointment_date` varchar(255) NOT NULL,
  `height` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT '''pending''',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `medical_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `resident_med_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resident_id` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_name` varchar(255) NOT NULL,
  `consultation_type` varchar(255) NOT NULL,
  `height` varchar(100) NOT NULL,
  `weight` varchar(100) NOT NULL,
  `appointment_date` varchar(255) NOT NULL,
  `appointment_number` int(20) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `resident_med_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Divorced') NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text NOT NULL,
  `vaccination_history` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `resident_id` varchar(20) NOT NULL,
  `role` enum('Resident','Admin') NOT NULL DEFAULT 'Resident',
  `height` varchar(100) NOT NULL,
  `weight` varchar(100) NOT NULL,
  `medical_history` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `resident_id` (`resident_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES ('23', 'juan@gmail.com', 'Juan', 'Dela', 'C', '1997-12-08', '27', '09100427913', 'Single', 'Male', 'Brgy. Poblacion', 'Phizer', '$2y$10$.QNs4tItcgPG4jrARinDNeV6FhoXKnkqol5gWqEetIny.0lRZz99C', 'residentID/FjU2lkcWYAgNG6d.jpg', 'RES-2025-001', 'Resident', '155', '66', '', '2025-02-08 12:15:55');
INSERT INTO `users` VALUES ('26', 'smith@gmail.com', 'Jane', 'Doe', 'Smith', '2000-04-30', '25', '0923423424', 'Married', 'Female', 'Tinago, Bayawan City', 'N/A', '$2y$10$Vy4pxu/I.Fs2cCTMNmFjjOY6h/zO1.Wg5v5PCurSKvhU2XWsUkEge', 'residentID/Screenshot_2024-09-27_104850-removebg-preview.png', 'RES-2025-002', 'Resident', '170', '80', '', '2025-04-30 11:07:34');
INSERT INTO `users` VALUES ('27', 'maria.santos@email.com', 'Maria', 'Santos', 'M', '1992-02-04', '33', '09987889087', 'Single', 'Female', '123 San Juan St., Taytay, Rizal', 'Hepatitis B', '$2y$10$ccw6mMroxIipN8XmzqFmOudtc.wP1Oxoq8I.hwXDxTP4SJVrrXt5G', '', 'RES-2025-003', 'Resident', '155', '55', '', '2025-05-04 16:50:25');
INSERT INTO `users` VALUES ('28', 'jose.rivera@gmail.com', 'Jose', 'Rivera', 'M', '1990-01-09', '35', '09262999121', 'Married', 'Male', '45 San Juan St. Taytay, Rizal', 'Flu, Hepatitis A\r\n', '$2y$10$lCq8jttXSQYvQhiZaWj.6eEeg8rb.Gzh.CdaovA5dqlH0/8fHjBmi', '', 'RES-2025-004', 'Resident', '160', '70', '', '2025-05-04 16:51:54');
INSERT INTO `users` VALUES ('29', 'ana.lopez@gmail.com', 'Ana', 'Lopez', 'L', '1996-12-05', '28', '09283253265', 'Single', 'Female', '67 San Juan St. Taytay, Rizal', 'N/A', '$2y$10$J1zo9MihOo.VZ7KOQn1ZC.xnXbY3Sorxhyyl16Uc8RKuGzbMPnqki', '', 'RES-2025-005', 'Resident', '155', '65', '', '2025-05-04 16:53:50');
INSERT INTO `users` VALUES ('30', 'carlos.gomez@gmail.com', 'Carlos', 'Gomez', 'D', '1998-09-19', '26', '09181218271', 'Single', 'Male', '89 San Juan St. Taytay, Rizal', 'N/A', '$2y$10$4bBy63NoyBSJxWuIzdUBpOq4Bhs80T7SCvZyxkTWSOeyFSfV7zOpy', '', 'RES-2025-006', 'Resident', '160', '65', '', '2025-05-04 16:55:02');
INSERT INTO `users` VALUES ('31', 'luciana.reyes@gmail.com', 'Luciana', 'Reyes', 'M', '2000-02-19', '25', '09898272827', 'Single', 'Female', '34 San Juan St. Taytay, Rizal', 'N/A', '$2y$10$eN9gl6TzxVXAf0biyriERe1aHFFntK5J5C6OdIP8v8KQkJr./O2kC', '', 'RES-2025-007', 'Resident', '160', '55', '', '2025-05-04 16:56:40');
INSERT INTO `users` VALUES ('32', 'roberto.perez@gmail.com', 'Roberto', 'Perez', 'P', '2000-12-20', '24', '09781827128', 'Married', 'Male', '12 San Juan ST. Taytay, Rizal', 'N/A', '$2y$10$BBelj3tn/AHnqSY8.GRVFeO3kxo9xMVag84UH2RHc4SmETR05LM3W', '', 'RES-2025-008', 'Resident', '160', '55', '', '2025-05-04 16:58:35');
INSERT INTO `users` VALUES ('33', 'elena.bautista@gmail.com', 'Elena', 'Bautista', 'M', '1980-01-22', '45', '09278192812', 'Widowed', 'Female', '67 San Juan St. Taytay, Rizal', 'Anti-Rabbies', '$2y$10$ZZRxbgrtj.wiU2vcDJxycOdDoFFREhICtjvHBxySjGUwGUzTZQqu6', '', 'RES-2025-009', 'Resident', '170', '60', '', '2025-05-04 16:59:59');
INSERT INTO `users` VALUES ('34', 'teresa.diaz@gmail.com', 'Teresa', 'Diaz', 'T', '1975-06-20', '49', '09817271828', 'Married', 'Female', '09 San Juan ST. Taytay, Rizal', 'Hepatitis A', '$2y$10$VCgrywkqldJzg1yQFoVmYu2U2wvOynoBTT.4hLlBFjTj1sY4k0hOO', '', 'RES-2025-010', 'Resident', '155', '66', '', '2025-05-04 17:01:16');
INSERT INTO `users` VALUES ('35', 'lebron@gmail.com', 'Leb', 'Fuentes', 'B', '2000-06-15', '24', '09090909090', 'Single', 'Male', '123 San Juan St Taytay, Rizal', 'N/A', '$2y$10$gqUI0.x.WEzPSRrGm0rLoef6HTX.sZCvC5heEG7pb3OV2cvBr0iWO', '', 'RES-2025-011', 'Resident', '170', '56', '', '2025-05-05 13:55:35');
INSERT INTO `users` VALUES ('36', 'yun@gmail.com', 'Roberto', 'Iglesias', 'S', '1999-01-27', '26', '09227363637', 'Single', 'Male', '123 San Juan St Taytay, Rizal', 'N/A', '$2y$10$uUpMwWihSuZLHckPlaz4fuXUfCu5efkBufONIEnDOPPCTV7bfcqMq', '', 'RES-2025-012', 'Resident', '170', '56', '', '2025-05-06 16:36:00');
INSERT INTO `users` VALUES ('37', 'henry@gmail.com', 'Henry', 'Diaz', 'S', '1990-04-05', '35', '09879872221', 'Married', 'Male', '129 San Juan St Taytay, Rizal', 'N/A', '$2y$10$r6CaRNi753j3q6dj/Lrzo.LOhEe8NpEmn0mVnsrDjLUQ5WA50IWUK', '', 'RES-2025-013', 'Resident', '167', '80', '', '2025-05-08 22:20:18');
