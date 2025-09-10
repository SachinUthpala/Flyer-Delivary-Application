<?php 




// Calculate dynamic statistics
require './Db/conn.php';
// Total Downloads
$sqlDownloads = "SELECT SUM(CAST(TotalDownloads AS UNSIGNED)) as total_downloads FROM customers";
$stmtDownloads = $pdo->prepare($sqlDownloads);
$stmtDownloads->execute();
$totalDownloads = $stmtDownloads->fetch(PDO::FETCH_ASSOC)['total_downloads'];

// New Users (users created in the last 30 days)
$sqlNewUsers = "SELECT COUNT(*) as new_users FROM users WHERE userId IN (
    SELECT DISTINCT CreateBy FROM customers WHERE createdDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
)";
$stmtNewUsers = $pdo->prepare($sqlNewUsers);
$stmtNewUsers->execute();
$newUsers = $stmtNewUsers->fetch(PDO::FETCH_ASSOC)['new_users'];

// Success Rate (percentage of customers with more than 5 downloads)
$sqlSuccessRate = "SELECT 
    (COUNT(CASE WHEN CAST(TotalDownloads AS UNSIGNED) > 5 THEN 1 END) * 100.0 / COUNT(*)) as success_rate 
    FROM customers";
$stmtSuccessRate = $pdo->prepare($sqlSuccessRate);
$stmtSuccessRate->execute();
$successRate = round($stmtSuccessRate->fetch(PDO::FETCH_ASSOC)['success_rate'], 0);

// Pending Requests (customers with less than 2 downloads)
$sqlPending = "SELECT COUNT(*) as pending_requests FROM customers WHERE CAST(TotalDownloads AS UNSIGNED) < 2";
$stmtPending = $pdo->prepare($sqlPending);
$stmtPending->execute();
$pendingRequests = $stmtPending->fetch(PDO::FETCH_ASSOC)['pending_requests'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ExpoFlyer Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <style>
        :root {
            --primary: #b42020;
            --secondary: #2036b4;
            --light: #f8f9fa;
            --dark: #212529;
            --sidebar-width: 250px;
            --header-height: 70px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        #sidebar {
            position: fixed;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(to bottom, var(--primary), #8a1818);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            left: 0;
            top: 0;
        }
        
        #sidebar.collapsed {
            margin-left: -250px;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        #sidebar .sidebar-header h3 {
            margin: 0;
            font-weight: 700;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 15px 25px;
            display: block;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        #sidebar ul li a:hover {
            background: rgba(0, 0, 0, 0.1);
            color: white;
        }
        
        #sidebar ul li a i {
            margin-right: 10px;
            width: 25px;
            text-align: center;
        }
        
        #sidebar ul li.active > a {
            background: rgba(0, 0, 0, 0.2);
            color: white;
            border-left: 4px solid white;
        }
        
        /* Header Styles */
        #header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 900;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            transition: all 0.3s ease;
        }
        
        #header.full-width {
            left: 0;
        }
        
        #content {
            margin-top: var(--header-height);
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s ease;
            min-height: calc(100vh - var(--header-height));
        }
        
        #content.full-width {
            margin-left: 0;
        }
        
        /* Cards */
        .dashboard-card {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            transition: transform 0.3s;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .card-primary {
            border-bottom: 4px solid var(--primary);
        }
        
        .card-secondary {
            border-bottom: 4px solid var(--secondary);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        /* Tables */
        .custom-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .custom-table th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            font-weight: 600;
            border-top: 1px solid #dee2e6;
        }
        
        .custom-table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        
        .custom-table tr:hover {
            background-color: rgba(180, 32, 32, 0.03);
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #7c1212;
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Toggle button for sidebar */
        #sidebarCollapse {
            background: var(--primary);
            border: none;
            border-radius: 4px;
            color: white;
            padding: 5px 10px;
            cursor: pointer;
        }
        
        /* User profile in header */
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
                top: var(--header-height);
                height: calc(100vh - var(--header-height));
            }
            
            #sidebar.mobile-show {
                margin-left: 0;
            }
            
            #header {
                left: 0;
            }
            
            #content {
                margin-left: 0;
            }
            
            .sidebar-toggle-area {
                display: block;
            }
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--secondary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-toggle-area {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }
        
        .header-toggle-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .header-toggle-btn {
                display: inline-block;
            }
        }
        
        .overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            top: var(--header-height);
            left: 0;
        }
        
        .overlay.active {
            display: block;
        }
        
        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(180, 32, 32, 0.25);
        }
        
        .form-select {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(180, 32, 32, 0.25);
        }
        
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 42px;
            color: #6c757d;
        }
        
        .submit-btn {
            background: var(--primary);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            background: #7c1212;
            transform: translateY(-2px);
        }
        
        .form-header {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-file-pdf me-2"></i>ExpoFlyer Admin</h3>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="./AdminUser.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li>
                <a href="./Mycus.php"><i class="fas fa-user"></i> My Customers</a>
            </li>
            <li>
                <a href="./Analitics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
            </li>
            <li>
                <a href="./AllCus.php"><i class="fas fa-user"></i>All Customers</a>
            </li>

            <li class="active">
                <a href="./AddUser.php"><i class="fas fa-users"></i>Add Users</a>
            </li>
            <li>
                <a href="./DeleteUser.php"><i class="fas fa-trash"></i>Delete Users</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-cog"></i>Remove All</a>
            </li>
            <li  >
                <a href="./Backend/logout.php"><i class="fas fa-sign-out-alt"></i>LogOut</a>
            </li>
        </ul>
    </nav>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Header -->
    <header id="header">
        <div>
            <button type="button" id="sidebarCollapse" class="btn btn-primary header-toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <span class="d-none d-md-inline">Add System User</span>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">A</div>
            <div>Admin User</div>
        </div>
    </header>

    <!-- Content -->
    <div id="content">
        <div class="container-fluid">
            <div class="sidebar-toggle-area d-md-none">
                <button type="button" id="mobileSidebarToggle" class="btn btn-primary">
                    <i class="fas fa-bars"></i> Toggle Sidebar
                </button>
            </div>
            
            <h2 class="section-title ">Add System User</h2>
            
            <!-- Statistics Cards -->
         <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card card-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h5 class="stat-number"><?php echo $totalDownloads; ?></h5>
                                    <p class="text-muted mb-0">Total Downloads</p>
                                </div>
                                <div class="col-4 text-end">
                                    <i class="fas fa-download stat-icon text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card card-secondary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h5 class="stat-number"><?php echo $newUsers; ?></h5>
                                    <p class="text-muted mb-0">New Users</p>
                                </div>
                                <div class="col-4 text-end">
                                    <i class="fas fa-user-plus stat-icon text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card card-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h5 class="stat-number"><?php echo $successRate; ?>%</h5>
                                    <p class="text-muted mb-0">Success Rate</p>
                                </div>
                                <div class="col-4 text-end">
                                    <i class="fas fa-chart-line stat-icon text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card card-secondary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h5 class="stat-number"><?php echo $pendingRequests; ?></h5>
                                    <p class="text-muted mb-0">Pending Requests</p>
                                </div>
                                <div class="col-4 text-end">
                                    <i class="fas fa-clock stat-icon text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Form -->
            <div class="row">
                <div class="col-12">
                    <div class="form-container">
                        <h3 class="form-header">Add New System User</h3>
                        <form id="userForm">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" name="userName" id="username" placeholder="Enter username" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="userEmail" id="email" placeholder="Enter email address" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="userPhone" id="phone" placeholder="Enter phone number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="userType" class="form-label">User Type</label>
                                    <select class="form-select" id="userType" name="userType" required>
                                        <option value="" selected disabled>Select user type</option>
                                        <option value="1">Super Admin</option>
                                        <option value="0">Normal Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control" name="userPassword" id="password" placeholder="Enter password" required>
                                        
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" name="addUser" class="btn submit-btn w-100">
                                        <i class="fas fa-user-plus me-2"></i>Add User
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery (required for toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const header = document.getElementById('header');
            const content = document.getElementById('content');
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('overlay');
            
            let isSidebarCollapsed = false;
            
            // Toggle sidebar function
            function toggleSidebar() {
                if (window.innerWidth < 768) {
                    // Mobile view
                    sidebar.classList.toggle('mobile-show');
                    overlay.classList.toggle('active');
                } else {
                    // Desktop view
                    isSidebarCollapsed = !isSidebarCollapsed;
                    
                    if (isSidebarCollapsed) {
                        sidebar.classList.add('collapsed');
                        header.classList.add('full-width');
                        content.classList.add('full-width');
                        sidebarCollapse.innerHTML = '<i class="fas fa-bars"></i>';
                    } else {
                        sidebar.classList.remove('collapsed');
                        header.classList.remove('full-width');
                        content.classList.remove('full-width');
                        sidebarCollapse.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                }
            }
            
            // Sidebar toggle functionality
            sidebarCollapse.addEventListener('click', toggleSidebar);
            
            // Mobile sidebar toggle
            mobileSidebarToggle.addEventListener('click', toggleSidebar);
            
            // Close sidebar when clicking outside on mobile
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-show');
                overlay.classList.remove('active');
            });
            
            // Close sidebar when clicking on a link (for mobile)
            const sidebarLinks = document.querySelectorAll('#sidebar ul li a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('mobile-show');
                        overlay.classList.remove('active');
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    // Reset mobile styles
                    sidebar.classList.remove('mobile-show');
                    overlay.classList.remove('active');
                    
                    // Apply desktop styles based on state
                    if (isSidebarCollapsed) {
                        sidebar.classList.add('collapsed');
                        header.classList.add('full-width');
                        content.classList.add('full-width');
                    } else {
                        sidebar.classList.remove('collapsed');
                        header.classList.remove('full-width');
                        content.classList.remove('full-width');
                    }
                } else {
                    // Mobile view - ensure sidebar is hidden by default
                    sidebar.classList.remove('collapsed');
                    header.classList.remove('full-width');
                    content.classList.remove('full-width');
                    sidebar.classList.remove('mobile-show');
                    overlay.classList.remove('active');
                }
            });

            // Form submission handling
            const userForm = document.getElementById("userForm");

            userForm.addEventListener("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(userForm);
                formData.append("addUser", true);

                const submitBtn = userForm.querySelector("button[type='submit']");
                const originalBtnHTML = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>Processing...`;

                fetch("Backend/AddUser.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    console.log("Server response:", data);
                    if (data.success) {
                        toastr.success(data.message || "User added successfully!");
                        userForm.reset();
                    } else {
                        toastr.error(data.message || "Failed to add user.");
                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                    toastr.error("Server error. Please try again.");
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHTML;
                });
            });
        });
    </script>
</body>
</html>