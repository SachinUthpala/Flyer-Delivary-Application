<?php
session_start();
require_once './Db/conn.php';

$url = 'https://eastlink.lk/';
$userName = $_SESSION['userName'];
$phone = $_SESSION['userPhone'];
$userEmail = $_SESSION['userEmail'];
// Generate the URL with parameters
$qrUrl = $url . '?userName=' . urlencode($userName) . '&userPhone=' . urlencode($phone);

$sql = "SELECT * FROM customers ";
$smtp = $pdo->prepare($sql);
$smtp->execute();





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
        
        .btn-danger {
            background: #dc3545;
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-danger:hover {
            background: #bb2d3b;
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
        
        /* QR Code Modal Styles */
        .qr-code-container {
            text-align: center;
            padding: 20px;
        }
        
        .qr-code {
            margin: 0 auto;
            padding: 15px;
            background: white;
            border-radius: 8px;
            display: inline-block;
        }
        
        .qr-url {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            word-break: break-all;
            font-size: 0.9rem;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        
        /* Delete Confirmation Modal */
        .confirmation-modal .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .confirmation-modal .modal-header {
            background: var(--primary);
            color: white;
        }
        
        .confirmation-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 15px;
        }
        
        /* Export Modal Styles */
        .export-option {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .export-option:hover {
            background-color: #f8f9fa;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .export-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .pdf-option {
            color: #dc3545;
        }
        
        .excel-option {
            color: #198754;
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
            <li >
                <a href="./Mycus.php"><i class="fas fa-user"></i> My Customers</a>
            </li>
            <li>
                <a href="./Analitics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
            </li>
            <li class="active"  >
                <a href="./AllCus.php"><i class="fas fa-user"></i>All Customers</a>
            </li>

            <li>
                <a href="./AddUser.php"><i class="fas fa-users"></i>Add Users</a>
            </li>
            <li >
                <a href="./DeleteUser.php"><i class="fas fa-trash"></i>Delete Users</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-cog"></i>Remove All</a>
            </li>
            <li>
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
            <span class="d-none d-md-inline">Dashboard</span>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">A</div>
            <div><?php echo $userName; ?></div>
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
            
            <h2 class="section-title">My All Customers</h2>
            
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
            
            <!-- Recent Downloads Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">My Customers</h5>
                            <button class="btn btn-outline-primary btn-sm" id="exportDataBtn">Export Data</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Company</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $smtp->fetch(PDO::FETCH_ASSOC)) { ?>
                                            <tr>
                                                <td><?php echo $row['customerName']; ?></td>
                                                <td><?php echo $row['customerEmail']; ?></td>
                                                <td><?php echo $row['customerPhone']; ?></td>
                                                <td><?php echo $row['Company']; ?></td>
                                               <td><?php echo $row['createdDate']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportOptionsModal" tabindex="-1" aria-labelledby="exportOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportOptionsModalLabel">Export Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-4">Select the format you want to export your data:</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="export-option pdf-option" data-format="pdf">
                                <div class="export-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <h5>PDF Format</h5>
                                <p class="text-muted">Best for printing and sharing</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="export-option excel-option" data-format="excel">
                                <div class="export-icon">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <h5>Excel Format</h5>
                                <p class="text-muted">Best for data analysis</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade confirmation-modal" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="confirmation-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4>Are you sure?</h4>
                    <p>You are about to delete user: <strong id="userToDelete"></strong></p>
                    <p>This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Yes, Delete User</a>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="qr-code-container">
                        <div class="qr-code" id="qrcode"></div>
                        <div class="qr-url mt-3">
                            <?php echo htmlspecialchars($qrUrl); ?>
                        </div>
                        <p class="text-muted mt-3">Scan this QR code to open the website with user parameters</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="downloadQR">Download QR Code</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
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

            // Initialize QR Code when modal is shown
            const qrCodeModal = document.getElementById('qrCodeModal');
            let qrcode = null;
            
            qrCodeModal.addEventListener('shown.bs.modal', function () {
                // Clear any existing QR code
                document.getElementById('qrcode').innerHTML = '';
                
                // Generate new QR code
                qrcode = new QRCode(document.getElementById('qrcode'), {
                    text: "<?php echo $qrUrl; ?>",
                    width: 200,
                    height: 200,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            });

            // Download QR Code functionality
            document.getElementById('downloadQR').addEventListener('click', function() {
                if (!qrcode) return;
                
                const canvas = document.querySelector('#qrcode canvas');
                if (!canvas) return;
                
                const link = document.createElement('a');
                link.download = 'expo-flyer-qr-code.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            // Delete User Confirmation Modal
            const deleteButtons = document.querySelectorAll('.delete-user');
            const deleteConfirmationModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            const userToDeleteElement = document.getElementById('userToDelete');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-userid');
                    const userName = this.getAttribute('data-username');
                    
                    // Set the user information in the modal
                    userToDeleteElement.textContent = userName;
                    
                    // Set the delete URL with the user ID
                    confirmDeleteBtn.href = `Backend/deleteUser.php?id=${userId}`;
                    
                    // Show the confirmation modal
                    deleteConfirmationModal.show();
                });
            });

            // Export Data Modal
            const exportDataBtn = document.getElementById('exportDataBtn');
            const exportOptionsModal = new bootstrap.Modal(document.getElementById('exportOptionsModal'));
            const exportOptions = document.querySelectorAll('.export-option');
            
            // Show export options modal when Export Data button is clicked
            exportDataBtn.addEventListener('click', function() {
                exportOptionsModal.show();
            });
            
            // Handle export option selection
            exportOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const format = this.getAttribute('data-format');
                    exportData(format);
                    exportOptionsModal.hide();
                });
            });
            
            // Function to handle data export
            function exportData(format) {
                // Create a form to submit the export request
                const form = document.createElement('form');
                form.method = 'POST';
                
                if (format === 'pdf') {
                    form.action = 'Backend/export_pdf.php';
                } else if (format === 'excel') {
                    form.action = 'Backend/export_excel.php';
                }
                
                // Add user email as a parameter to identify which data to export
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'userEmail';
                input.value = '<?php echo $userEmail; ?>';
                form.appendChild(input);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        });
    </script>
</body>
</html>-