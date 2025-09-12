# ExpoFlyer Customer Data

This file contains sample customer data for the ExpoFlyer Admin Panel project.  
Each line represents a customer with the following fields separated by tabs:

- **Name**
- **Email**
- **Phone**
- **Company**
- **Date**

---


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

---

## Usage

You can use this data for testing, importing into your database, or as sample input for





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

