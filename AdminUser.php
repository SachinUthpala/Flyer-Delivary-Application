<?php

session_start();


// Get the full current page URL
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$currentUrl .= "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

echo $currentUrl;
// Example output: https://mediumseagreen-falcon-577404.hostingersite.com/users/admin.php

// Get only the base domain
$parsedUrl = parse_url($currentUrl);
$baseUrl = $parsedUrl['scheme'] . "://" . $parsedUrl['host'];



$url = $baseUrl;
$userName = $_SESSION['userName'];
$phone = $_SESSION['userPhone'];
$userEmail = $_SESSION['userEmail'];

if(!$_SESSION['userName']){
    header('Location: ./signIn.php');
}

// Generate the URL with parameters
$qrUrl = $url . '?userName=' . urlencode($userName) . '&userPhone=' . urlencode($phone).'&userEmail=' . urlencode($userEmail);




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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-file-pdf me-2"></i>ExpoFlyer Admin</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active" >
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

            <li  >
                <a href="./AddUser.php"><i class="fas fa-users"></i>Add Users</a>
            </li>
            <li  >
                <a href="./DeleteUser.php"><i class="fas fa-trash"></i>Delete Users</a>
            </li>
            <li  >
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
            <span class="d-none d-md-inline">Dashboard</span>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">A</div>
            <div><?php echo $_SESSION['userName']; ?></div>
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
            
        
            <div class="dashboard-header ">
                <h2 class="section-title mb-0">Dashboard Overview</h2>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                    <i class="fas fa-qrcode me-2"></i>Generate QR Code
                </button>
            </div>
            
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
                            <h5 class="mb-0">Recent Downloads</h5>
                            <button class="btn btn-outline-primary btn-sm">Export Data</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Email</th>
                                             <th>Phone</th>
                                            <th>Created Date</th>
                                            <th>Num Of Flyers</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>john.doe@example.com</td>
                                            <td>Product Catalog</td>
                                            <td>Jun 12, 2023</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">View</button>
                                            </td>
                                        </tr>
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
        });
    </script>
</body>
</html>