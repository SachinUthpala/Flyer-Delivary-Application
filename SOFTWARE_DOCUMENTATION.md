# Exhibition Customer Management System (ExpoFlyer Delivery)
## Software Documentation

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Technology Stack](#technology-stack)
4. [Folder Structure](#folder-structure)
5. [Database Schema](#database-schema)
6. [Implementation Details](#implementation-details)
7. [API Endpoints](#api-endpoints)
8. [User Interface Components](#user-interface-components)
9. [Security Features](#security-features)
10. [Test Data](#test-data)
11. [Installation Guide](#installation-guide)
12. [Configuration](#configuration)
13. [Deployment](#deployment)
14. [Maintenance](#maintenance)

---

## Project Overview

### Purpose
The Exhibition Customer Management System (ExpoFlyer Delivery) is a web-based application designed to manage customer interactions at exhibitions. The system allows exhibition staff to collect customer information and automatically send requested marketing materials (flyers) via email.

### Key Features
- **Customer Registration**: Collect customer details through a user-friendly form
- **Flyer Distribution**: Send PDF flyers via email based on customer selections
- **Admin Dashboard**: Comprehensive management interface for exhibition staff
- **User Management**: Multi-level admin system with role-based access
- **Analytics**: Real-time statistics and reporting
- **QR Code Generation**: Generate QR codes for easy booth access
- **Data Export**: Export customer data in PDF format

### Target Users
- Exhibition booth staff
- Marketing teams
- Event organizers
- Customer service representatives

---

## System Architecture

### Architecture Pattern
The system follows a **3-tier architecture**:
1. **Presentation Layer**: HTML/CSS/JavaScript frontend
2. **Business Logic Layer**: PHP backend processing
3. **Data Layer**: MySQL database

### System Flow
```
User Access → Authentication → Dashboard → Customer Form → Email Processing → Database Storage
```

### Core Components
- **Frontend Interface**: Bootstrap-based responsive UI
- **Authentication System**: Session-based login/logout
- **Email Service**: PHP mail() function for flyer delivery
- **PDF Generation**: DomPDF library for report generation
- **Database Management**: PDO-based MySQL operations

---

## Technology Stack

### Backend Technologies
- **PHP 7.4+**: Server-side scripting language
- **MySQL**: Relational database management system
- **PDO**: PHP Data Objects for database abstraction
- **Composer**: Dependency management

### Frontend Technologies
- **HTML5**: Markup language
- **CSS3**: Styling with custom CSS variables
- **JavaScript (ES6+)**: Client-side scripting
- **Bootstrap 5.3.0**: CSS framework for responsive design
- **Font Awesome 6.4.0**: Icon library
- **Chart.js**: Data visualization library
- **QRCode.js**: QR code generation library

### Third-Party Libraries
- **DomPDF 3.1**: PDF generation and manipulation
- **Toastr.js**: Notification system
- **jQuery 3.6.0**: JavaScript library (for Toastr)

### Development Environment
- **XAMPP**: Local development server
- **Apache**: Web server
- **MySQL**: Database server
- **PHP**: Server-side language

---

## Folder Structure

```
ExsibitionCus/
├── Backend/                          # Backend processing files
│   ├── AddUser.php                   # User creation endpoint
│   ├── composer.json                 # Composer dependencies
│   ├── composer.lock                 # Dependency lock file
│   ├── deleteUser.php                # User deletion endpoint
│   ├── export_pdf.php                # PDF export functionality
│   ├── login.php                     # Authentication endpoint
│   ├── logout.php                    # Session termination
│   └── vendor/                       # Composer dependencies
│       ├── autoload.php              # Autoloader
│       ├── composer/                 # Composer metadata
│       ├── dompdf/                   # PDF generation library
│       ├── masterminds/              # HTML5 parser
│       └── sabberworm/               # CSS parser
├── Db/                               # Database configuration
│   └── conn.php                      # Database connection
├── Flyer/                            # PDF flyer storage
│   ├── pdf1.pdf                      # Product Catalog
│   ├── pdf2.pdf                      # Product Brochure
│   ├── pdf3.pdf                      # Price List
│   └── pdf4.pdf                      # Special Offers
├── Img/                              # Image assets (empty)
├── AddUser.php                       # User management interface
├── AdminUser.php                     # Main admin dashboard
├── AllCus.php                        # All customers view
├── Analitics.php                     # Analytics dashboard
├── DeleteUser.php                    # User deletion interface
├── index.php                         # Main customer form
├── Mycus.php                         # User's customers view
├── README.md                         # Project documentation
├── send_files.php                    # Email processing endpoint
├── signIn.php                        # Login interface
└── style.css                         # Global stylesheet
```

---

## Database Schema

### Database: `excus`

#### Table: `users`
```sql
CREATE TABLE users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    userName VARCHAR(255) NOT NULL,
    userMail VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    AdmnAccess TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Fields:**
- `userId`: Primary key, auto-increment
- `userName`: User's display name
- `userMail`: Email address (unique)
- `phone`: Contact number
- `password`: Hashed password
- `AdmnAccess`: Admin level (1=Super Admin, 0=Normal Admin)

#### Table: `customers`
```sql
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customerName VARCHAR(255) NOT NULL,
    customerEmail VARCHAR(255) NOT NULL,
    customerPhone VARCHAR(20) NOT NULL,
    Company VARCHAR(255),
    TotalDownloads INT DEFAULT 0,
    CreateBy VARCHAR(255) NOT NULL,
    createdDate DATE NOT NULL
);
```

**Fields:**
- `id`: Primary key, auto-increment
- `customerName`: Customer's full name
- `customerEmail`: Customer's email address
- `customerPhone`: Customer's phone number
- `Company`: Customer's company name (optional)
- `TotalDownloads`: Number of flyers requested
- `CreateBy`: Email of the admin who created the record
- `createdDate`: Date when record was created

### Database Connection Configuration
```php
// Local Development
$serverName = "localhost";
$userName = "root";
$password = "";
$dbName = "excus";

// Production (commented)
// $serverName = "localhost";
// $userName = "u115172255_mcsUser";
// $password = "c8M8UMp@&";
// $dbName = "u115172255_MCS";
```

---

## Implementation Details

### Authentication System

#### Login Process (`Backend/login.php`)
```php
// Input validation
$emailOrPhone = trim($_POST['emailOrPhone'] ?? '');
$password = $_POST['userPassword'] ?? '';

// Database query with prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE userMail = ? OR phone = ?");
$stmt->execute([$emailOrPhone, $emailOrPhone]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Password verification
if ($user && password_verify($password, $user['password'])) {
    // Session creation
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['userName'] = $user['userName'];
    $_SESSION['userEmail'] = $user['userMail'];
    $_SESSION['userPhone'] = $user['phone'];
    $_SESSION['userType'] = $user['AdmnAccess'];
}
```

#### Session Management
- **Session Start**: `session_start()` in all protected pages
- **Session Variables**: User ID, name, email, phone, admin type
- **Session Destruction**: Complete cleanup in `logout.php`

### Customer Data Processing

#### Form Submission (`send_files.php`)
```php
// Data validation
$name = $_POST['name'] ?? '';
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$company = trim($_POST['company'] ?? '');
$createdBy = trim($_POST['createdBy'] ?? '');
$flyers = json_decode($_POST['flyers'] ?? '[]', true);

// Database insertion
$sql = "INSERT INTO `customers` 
    (`customerName`, `customerEmail`, `customerPhone`, `Company`, `TotalDownloads`, `CreateBy`, `createdDate`) 
    VALUES (:customerName, :customerEmail, :customerPhone, :Company, :TotalDownloads, :CreateBy, :createdDate)";
```

#### Email Processing
```php
// Email headers with MIME multipart
$boundary = md5(time());
$headers = "From: ExpoFlyer <no-reply@expoflyer.com>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

// PDF attachment processing
foreach ($flyers as $flyer) {
    $filename = basename($flyer) . '.pdf';
    $filepath = $flyerDir . $filename;
    
    if (file_exists($filepath)) {
        $fileContent = chunk_split(base64_encode(file_get_contents($filepath)));
        // Add to email body
    }
}
```

### User Management

#### User Creation (`Backend/AddUser.php`)
```php
// Input validation and sanitization
$userName = trim($_POST['userName'] ?? '');
$email = trim($_POST['userEmail'] ?? '');
$phone = trim($_POST['userPhone'] ?? '');
$password = $_POST['userPassword'] ?? '';
$userType = (int) ($_POST['userType'] ?? 0);

// Duplicate email check
$check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE userMail = ?");
$check->execute([$email]);

// Password hashing
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Database insertion
$stmt = $pdo->prepare("INSERT INTO users (userName, userMail, phone, password, AdmnAccess) 
                       VALUES (?, ?, ?, ?, ?)");
```

#### User Deletion (`Backend/deleteUser.php`)
```php
// ID validation
$_id = $_GET['id'];

// Prepared statement for deletion
$sql = "DELETE FROM users WHERE userId = :userId";
$smtp = $pdo->prepare($sql);
$smtp->execute([':userId' => $_id]);
```

### PDF Generation

#### Export Functionality (`Backend/export_pdf.php`)
```php
// DomPDF setup
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// HTML content generation
$html = '<h1>Customer Data</h1>';
$html .= '<table border="1" cellspacing="0" cellpadding="5">';
// Table content generation

// PDF rendering
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('customers.pdf', ['Attachment' => true]);
```

---

## API Endpoints

### Authentication Endpoints

#### POST `/Backend/login.php`
**Purpose**: User authentication
**Parameters**:
- `emailOrPhone` (string): User email or phone number
- `userPassword` (string): User password
- `loginUser` (boolean): Form submission flag

**Response**: Redirect to dashboard or login page with error message

#### GET `/Backend/logout.php`
**Purpose**: Session termination
**Response**: Redirect to login page

### User Management Endpoints

#### POST `/Backend/AddUser.php`
**Purpose**: Create new system user
**Parameters**:
- `userName` (string): User display name
- `userEmail` (string): User email address
- `userPhone` (string): User phone number
- `userPassword` (string): User password
- `userType` (integer): Admin level (0 or 1)
- `addUser` (boolean): Form submission flag

**Response**: JSON
```json
{
    "success": true/false,
    "message": "Success/Error message"
}
```

#### GET `/Backend/deleteUser.php?id={userId}`
**Purpose**: Delete system user
**Parameters**:
- `id` (integer): User ID to delete

**Response**: Redirect to user management page

### Customer Management Endpoints

#### POST `/send_files.php`
**Purpose**: Process customer form and send flyers
**Parameters**:
- `name` (string): Customer name
- `email` (string): Customer email
- `phone` (string): Customer phone
- `company` (string): Customer company (optional)
- `createdBy` (string): Admin email who created the record
- `flyers` (JSON string): Array of selected flyer IDs

**Response**: JSON
```json
{
    "success": true/false,
    "message": "Success/Error message"
}
```

#### POST `/Backend/export_pdf.php`
**Purpose**: Export customer data as PDF
**Parameters**:
- `userEmail` (string): Admin email to filter data

**Response**: PDF file download

---

## User Interface Components

### Frontend Architecture

#### CSS Framework
- **Bootstrap 5.3.0**: Responsive grid system and components
- **Custom CSS Variables**: Consistent color scheme
- **Font Awesome Icons**: Visual indicators and actions

#### Color Scheme
```css
:root {
    --primary: #b42020;      /* Red primary color */
    --secondary: #2036b4;    /* Blue secondary color */
    --light: #f8f9fa;        /* Light background */
    --dark: #212529;         /* Dark text */
}
```

### Key UI Components

#### 1. Login Interface (`signIn.php`)
- **Responsive Design**: Mobile-first approach
- **Form Validation**: Client-side and server-side validation
- **Error Handling**: Session-based error messages
- **Social Login Placeholders**: Facebook and Google buttons (UI only)

#### 2. Customer Form (`index.php`)
- **Multi-step Interface**: Form with flyer selection
- **Interactive Flyer Cards**: Click-to-select functionality
- **Real-time Validation**: JavaScript form validation
- **Success Feedback**: AJAX-based submission with loading states

#### 3. Admin Dashboard (`AdminUser.php`)
- **Sidebar Navigation**: Collapsible navigation menu
- **Statistics Cards**: Real-time data visualization
- **QR Code Generation**: Modal-based QR code display
- **Responsive Tables**: Mobile-optimized data tables

#### 4. Analytics Dashboard (`Analitics.php`)
- **Chart.js Integration**: Multiple chart types (bar, line, pie, doughnut)
- **Interactive Charts**: Click-to-switch chart types
- **Data Export**: PDF export functionality
- **Summary Statistics**: Key performance indicators

### JavaScript Functionality

#### Form Handling
```javascript
// AJAX form submission
fetch('send_files.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Show success message
        document.getElementById('successAlert').style.display = 'block';
    } else {
        alert('Error: ' + data.message);
    }
});
```

#### Sidebar Management
```javascript
// Responsive sidebar toggle
function toggleSidebar() {
    if (window.innerWidth < 768) {
        // Mobile view
        sidebar.classList.toggle('mobile-show');
        overlay.classList.toggle('active');
    } else {
        // Desktop view
        isSidebarCollapsed = !isSidebarCollapsed;
        // Apply appropriate classes
    }
}
```

#### QR Code Generation
```javascript
// QR Code generation
qrcode = new QRCode(document.getElementById('qrcode'), {
    text: qrUrl,
    width: 200,
    height: 200,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});
```

---

## Security Features

### Authentication Security
- **Password Hashing**: PHP `password_hash()` with `PASSWORD_DEFAULT`
- **Session Management**: Secure session handling with proper cleanup
- **Input Validation**: Server-side validation for all user inputs
- **SQL Injection Prevention**: PDO prepared statements throughout

### Data Protection
- **Input Sanitization**: `trim()` and `htmlspecialchars()` for user inputs
- **XSS Prevention**: Output escaping in HTML contexts
- **CSRF Protection**: Session-based form validation
- **File Upload Security**: Restricted file types and validation

### Access Control
- **Role-based Access**: Admin levels (Super Admin vs Normal Admin)
- **Session Validation**: Check for valid sessions on protected pages
- **Redirect Protection**: Unauthorized access redirects to login

### Security Headers
```php
// Content Security Policy (recommended)
header("Content-Security-Policy: default-src 'self'");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
```

---

## Test Data

### Sample User Accounts

#### Super Admin Account
```sql
INSERT INTO users (userName, userMail, phone, password, AdmnAccess) VALUES 
('Super Admin', 'admin@expoflyer.com', '0771234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
```
**Login Credentials:**
- Email: `admin@expoflyer.com`
- Password: `password`

#### Normal Admin Account
```sql
INSERT INTO users (userName, userMail, phone, password, AdmnAccess) VALUES 
('John Doe', 'john@expoflyer.com', '0777654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0);
```
**Login Credentials:**
- Email: `john@expoflyer.com`
- Password: `password`

### Sample Customer Data
```sql
INSERT INTO customers (customerName, customerEmail, customerPhone, Company, TotalDownloads, CreateBy, createdDate) VALUES 
('Alice Johnson', 'alice@company.com', '0771111111', 'Tech Solutions Ltd', 3, 'admin@expoflyer.com', '2024-01-15'),
('Bob Smith', 'bob@enterprise.com', '0772222222', 'Enterprise Corp', 2, 'john@expoflyer.com', '2024-01-16'),
('Carol Davis', 'carol@startup.com', '0773333333', 'Startup Inc', 4, 'admin@expoflyer.com', '2024-01-17'),
('David Wilson', 'david@business.com', '0774444444', 'Business Group', 1, 'john@expoflyer.com', '2024-01-18');
```

### Test Flyer Files
The system includes 4 sample PDF flyers in the `/Flyer/` directory:
- `pdf1.pdf`: Product Catalog (12 pages)
- `pdf2.pdf`: Product Brochure (4 pages)
- `pdf3.pdf`: Price List (2 pages)
- `pdf4.pdf`: Special Offers (Limited time)

### Database Test Queries

#### Get All Users
```sql
SELECT userId, userName, userMail, phone, 
       CASE WHEN AdmnAccess = 1 THEN 'Super Admin' ELSE 'Normal Admin' END as userType
FROM users;
```

#### Get Customer Statistics
```sql
SELECT 
    COUNT(*) as total_customers,
    SUM(TotalDownloads) as total_downloads,
    AVG(TotalDownloads) as avg_downloads,
    COUNT(DISTINCT CreateBy) as active_admins
FROM customers;
```

#### Get Customers by Admin
```sql
SELECT 
    u.userName as admin_name,
    COUNT(c.id) as customers_created,
    SUM(c.TotalDownloads) as total_downloads
FROM users u
LEFT JOIN customers c ON u.userMail = c.CreateBy
GROUP BY u.userId, u.userName;
```

---

## Installation Guide

### Prerequisites
- **XAMPP** (Apache, MySQL, PHP 7.4+)
- **Composer** (for dependency management)
- **Web Browser** (Chrome, Firefox, Safari, Edge)

### Step-by-Step Installation

#### 1. Environment Setup
```bash
# Download and install XAMPP
# Start Apache and MySQL services
# Navigate to htdocs directory
cd C:\xampp\htdocs\
```

#### 2. Project Setup
```bash
# Clone or copy project files
# Navigate to project directory
cd ExsibitionCus/

# Install Composer dependencies
cd Backend/
composer install
cd ..
```

#### 3. Database Setup
```sql
-- Create database
CREATE DATABASE excus;

-- Use database
USE excus;

-- Create users table
CREATE TABLE users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    userName VARCHAR(255) NOT NULL,
    userMail VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    AdmnAccess TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customerName VARCHAR(255) NOT NULL,
    customerEmail VARCHAR(255) NOT NULL,
    customerPhone VARCHAR(20) NOT NULL,
    Company VARCHAR(255),
    TotalDownloads INT DEFAULT 0,
    CreateBy VARCHAR(255) NOT NULL,
    createdDate DATE NOT NULL
);

-- Insert test data (see Test Data section)
```

#### 4. Configuration
```php
// Update Db/conn.php with your database credentials
$serverName = "localhost";
$userName = "root";
$password = ""; // Your MySQL password
$dbName = "excus";
```

#### 5. File Permissions
```bash
# Ensure proper permissions for uploads and logs
chmod 755 Flyer/
chmod 644 Flyer/*.pdf
```

#### 6. Email Configuration
```php
// Configure PHP mail settings in php.ini
// For production, consider using SMTP
// Update send_files.php with proper email settings
```

### Verification Steps
1. **Database Connection**: Check if database connects successfully
2. **Login Test**: Try logging in with test credentials
3. **Form Submission**: Test customer form submission
4. **Email Delivery**: Verify email sending functionality
5. **PDF Generation**: Test PDF export feature

---

## Configuration

### Database Configuration (`Db/conn.php`)
```php
<?php
$serverName = "localhost";
$userName = "root";
$password = ""; // Set your MySQL password
$dbName = "excus";

try {
    $pdo = new PDO("mysql:host=$serverName;dbname=$dbName", $userName, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection Failed: " . $e->getMessage();
    exit;
}
?>
```

### Email Configuration
```php
// In send_files.php, update email settings
$headers = "From: Your Company <noreply@yourcompany.com>\r\n";
$headers .= "Reply-To: support@yourcompany.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
```

### Application Settings
```php
// Timezone setting
date_default_timezone_set('Asia/Colombo'); // Adjust for your timezone

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // For HTTPS only
```

### File Upload Settings
```php
// PHP configuration for file uploads
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M
```

---

## Deployment

### Production Deployment Checklist

#### 1. Server Requirements
- **Web Server**: Apache 2.4+ or Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **SSL Certificate**: For HTTPS
- **Domain Name**: Configured DNS

#### 2. Security Hardening
```php
// Production database configuration
$serverName = "localhost";
$userName = "production_user";
$password = "strong_password_here";
$dbName = "production_db";

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');

// Disable error display
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
```

#### 3. File Structure
```
/var/www/html/expoflyer/
├── public_html/          # Web-accessible files
│   ├── index.php
│   ├── signIn.php
│   ├── AdminUser.php
│   └── ...
├── private/              # Protected files
│   ├── Backend/
│   ├── Db/
│   └── vendor/
└── uploads/              # File uploads
    └── Flyer/
```

#### 4. Apache Configuration
```apache
<VirtualHost *:443>
    ServerName expoflyer.yourdomain.com
    DocumentRoot /var/www/html/expoflyer/public_html
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    
    <Directory /var/www/html/expoflyer/public_html>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

#### 5. Database Migration
```sql
-- Create production database
CREATE DATABASE production_excus;
CREATE USER 'expoflyer_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON production_excus.* TO 'expoflyer_user'@'localhost';
FLUSH PRIVILEGES;

-- Import database structure and data
USE production_excus;
SOURCE /path/to/database_backup.sql;
```

### Performance Optimization

#### 1. Caching
```php
// Enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

#### 2. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_customers_created_by ON customers(CreateBy);
CREATE INDEX idx_customers_created_date ON customers(createdDate);
CREATE INDEX idx_users_email ON users(userMail);
```

#### 3. File Compression
```apache
# Enable GZIP compression
LoadModule deflate_module modules/mod_deflate.so
<Location />
    SetOutputFilter DEFLATE
    SetEnvIfNoCase Request_URI \
        \.(?:gif|jpe?g|png)$ no-gzip dont-vary
    SetEnvIfNoCase Request_URI \
        \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
</Location>
```

---

## Maintenance

### Regular Maintenance Tasks

#### 1. Database Maintenance
```sql
-- Weekly database optimization
OPTIMIZE TABLE users, customers;

-- Monthly cleanup of old sessions
DELETE FROM sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 1 MONTH);

-- Backup database
mysqldump -u username -p excus > backup_$(date +%Y%m%d).sql
```

#### 2. Log Monitoring
```bash
# Monitor error logs
tail -f /var/log/apache2/error.log
tail -f /var/log/php_errors.log

# Monitor access logs
tail -f /var/log/apache2/access.log
```

#### 3. Security Updates
```bash
# Update system packages
sudo apt update && sudo apt upgrade

# Update Composer dependencies
cd Backend/
composer update

# Check for PHP security updates
php -v
```

#### 4. Performance Monitoring
```sql
-- Monitor database performance
SHOW PROCESSLIST;
SHOW STATUS LIKE 'Slow_queries';

-- Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'excus';
```

### Backup Strategy

#### 1. Database Backups
```bash
#!/bin/bash
# Daily backup script
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p excus > /backups/db_backup_$DATE.sql
gzip /backups/db_backup_$DATE.sql

# Keep only last 30 days
find /backups -name "db_backup_*.sql.gz" -mtime +30 -delete
```

#### 2. File Backups
```bash
#!/bin/bash
# Weekly file backup
DATE=$(date +%Y%m%d)
tar -czf /backups/files_backup_$DATE.tar.gz /var/www/html/expoflyer/
```

#### 3. Configuration Backups
```bash
# Backup configuration files
cp /etc/apache2/sites-available/expoflyer.conf /backups/
cp /etc/php/7.4/apache2/php.ini /backups/
```

### Troubleshooting Guide

#### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `Db/conn.php`
   - Verify MySQL service is running
   - Check firewall settings

2. **Email Not Sending**
   - Verify PHP mail configuration
   - Check SMTP settings
   - Test with simple mail() function

3. **PDF Generation Issues**
   - Ensure DomPDF is properly installed
   - Check file permissions
   - Verify memory limits

4. **Session Issues**
   - Check session configuration
   - Verify session directory permissions
   - Clear browser cookies

5. **File Upload Problems**
   - Check PHP upload limits
   - Verify directory permissions
   - Test with small files first

### Support and Documentation

#### Contact Information
- **Technical Support**: support@expoflyer.com
- **Documentation**: Available in project repository
- **Issue Tracking**: GitHub issues or internal ticketing system

#### Version History
- **v1.0.0**: Initial release with basic functionality
- **v1.1.0**: Added analytics dashboard
- **v1.2.0**: Enhanced security features
- **v1.3.0**: Added PDF export functionality

---

## Conclusion

The Exhibition Customer Management System (ExpoFlyer Delivery) provides a comprehensive solution for managing customer interactions at exhibitions. The system's modular architecture, robust security features, and user-friendly interface make it suitable for various exhibition environments.

### Key Strengths
- **Scalable Architecture**: Easy to extend and modify
- **Security-First Design**: Multiple layers of security
- **Responsive Interface**: Works on all device types
- **Comprehensive Analytics**: Real-time insights and reporting
- **Easy Deployment**: Simple installation and configuration

### Future Enhancements
- **Mobile App**: Native mobile application
- **Advanced Analytics**: Machine learning insights
- **Integration APIs**: Third-party system integration
- **Multi-language Support**: Internationalization
- **Cloud Deployment**: AWS/Azure deployment options

This documentation provides a complete guide for understanding, implementing, and maintaining the Exhibition Customer Management System.

---

**Document Version**: 1.0  
**Last Updated**: January 2024  
**Author**: Development Team  
**Review Status**: Approved
