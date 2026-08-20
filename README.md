# BHRSAGQ

## Barangay Health Record System With Automated Generated ID and Queue System

BHRSAGQ is a web-based **Barangay Health Record System** designed to help manage resident information, health records, appointments, and patient queuing in a barangay health center.

The system combines digital health-record management with an **automatically generated resident ID** and a **queue/appointment system** to help organize health-center operations and reduce manual record keeping.

## Features

### 🏥 Resident and Health Record Management
- Register and manage barangay residents.
- Store resident profile and personal information.
- Search for existing residents.
- View and update resident information.
- Delete resident records when authorized.
- Maintain medical/health records associated with residents.
- View medical transaction/history information.

### 🪪 Automated Resident ID
- Automatically generate a unique resident ID during registration.
- Generate resident ID cards using stored resident information.
- Store resident ID card/profile images.
- Support resident identification through the system's generated ID.

### 🎫 Appointment and Queue System
- Create and manage health-center appointments.
- Organize patients through a queue.
- Track appointment/queue status.
- Mark queue entries as completed or cancelled.
- Maintain appointment/medical transaction logs.

### 👤 User and Administration
- Administrator login and logout.
- Admin account management.
- Add administrators.
- Manage administrator profile information.
- Dashboard for monitoring system information and activities.

### 📊 Dashboard and Records
- Centralized administrative dashboard.
- Resident record viewing and management.
- Appointment and queue monitoring.
- Medical record management.
- System data presented through a web-based interface.

### 💾 Data Management
- MySQL database support.
- Database backup and restore functionality.
- Separate model/database logic for major system operations.

## System Modules

The project is organized into several main areas:

```text
BHRSAGQ/
├── admin/                 # Administrative portal
│   ├── dashboard.php
│   └── includes/         # Admin modules and actions
├── models/                # Application/business logic
├── residents/             # Resident portal/dashboard
├── Database/              # Database connection
├── landing/               # Public-facing pages and assets
├── residentID/            # Generated resident ID/profile assets
├── uploads/               # Uploaded profile images
├── form.php               # Registration/form interface
├── index.php              # Main entry point
├── med_appointment_log.php
└── bhrsagq_db.sql         # Database structure/data
```

## Technology Stack

- **PHP** — server-side application logic
- **MySQL** — database management
- **HTML5** — page structure
- **CSS3** — styling
- **JavaScript** — client-side functionality
- **Bootstrap** — responsive user interface
- **jQuery** — client-side utilities
- **DataTables** — tabular data presentation
- **Chart.js** — dashboard/data visualization
- **Font Awesome** — icons

## Database

The project includes the database SQL file:

```text
bhrsagq_db.sql
```

There is also a database SQL copy under:

```text
landing/sql/db.sql
```

Before running the application, import the appropriate SQL file into MySQL/MariaDB and configure the database connection in the project's database configuration.

## Local Installation

### Requirements

A typical local development environment can use:

- XAMPP, WAMP, or another PHP development stack
- Apache
- PHP
- MySQL/MariaDB
- A modern web browser

### 1. Clone the repository

```bash
git clone https://github.com/YOUR-USERNAME/BHRSAGQ.git
cd BHRSAGQ
```

Replace `YOUR-USERNAME` with your GitHub username.

### 2. Place the project in your web server directory

For XAMPP, place the project inside:

```text
C:\xampp\htdocs\
```

The resulting directory can be:

```text
C:\xampp\htdocs\BHRSAGQ\
```

### 3. Start Apache and MySQL

Open the XAMPP Control Panel and start:

- Apache
- MySQL

### 4. Create the database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Create the database expected by the project and import:

```text
bhrsagq_db.sql
```

### 5. Configure the database connection

Check:

```text
Database/Database.php
```

Update the database host, username, password, and database name if your local MySQL configuration differs.

### 6. Run the system

Open the project through your local server, for example:

```text
http://localhost/BHRSAGQ/
```

## Main Workflow

A typical system workflow is:

```text
Resident Registration
        ↓
Automated Resident ID Generation
        ↓
Resident Record Stored
        ↓
Appointment / Queue Registration
        ↓
Patient Queue Management
        ↓
Health Service / Medical Transaction
        ↓
Medical Record Updated
        ↓
Appointment Completed or Cancelled
```

## Project Structure

### `admin/`

Contains the administrative side of the system, including:

- Dashboard
- Resident records
- Appointments
- Queue management
- Medical records
- Account/profile management
- Administrator management
- Queue completion and cancellation actions
- Resident ID generation

### `models/`

Contains the application's processing and database-related logic, including modules for:

- Authentication
- Resident registration
- Resident search
- Resident updates
- Resident deletion
- Appointments
- Medical transactions
- Card/ID generation
- Backup and restore operations

### `landing/`

Contains the public-facing interface and frontend assets, including:

- Bootstrap
- JavaScript
- CSS
- Images
- Icons
- DataTables
- Chart.js
- Registration-related scripts

### `residents/`

Contains the resident-facing portion of the system.

## Security Notes

This project was developed as an academic/local web application. Before deploying it to a public production server, additional security hardening should be performed, including:

- Password hashing and secure authentication practices
- Input validation and sanitization
- Protection against SQL injection
- CSRF protection
- Access-control enforcement
- Secure session configuration
- File-upload validation
- Removal of development/test accounts and personal data
- Protection of health information and other sensitive resident data

**Do not commit real resident health records, passwords, personal information, or private uploaded images to a public GitHub repository.**

## Privacy

BHRSAGQ handles potentially sensitive resident and health information. If publishing the source code publicly, make sure the repository contains only:

- Source code
- Database schema
- Sample/test data
- Documentation

Do not publish actual resident records or personally identifiable information.

## Project Purpose

BHRSAGQ was created to provide a centralized digital system for barangay health-center operations. Its goal is to replace or reduce manual processes involved in maintaining resident health records, generating resident identification, and organizing patient appointments and queues.

## Status

**Academic / Educational Project**

This repository represents the BHRSAGQ web application and its supporting database and frontend assets.

## License

No open-source license has been specified for this project yet.

If you intend to allow others to reuse, modify, or distribute the code, add an appropriate license such as MIT before publishing the repository.
"# BHRSAGQ" 
