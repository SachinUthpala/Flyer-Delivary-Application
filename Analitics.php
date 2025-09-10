<?php
session_start();
require './Db/conn.php';

// Get the full current page URL
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$currentUrl .= "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

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

// Get user creation statistics
$sql = "SELECT 
    CreateBy,
    COUNT(*) AS total_records
FROM 
    customers
GROUP BY 
    CreateBy
ORDER BY 
    total_records DESC;
";
$smtp = $pdo->prepare($sql);
$smtp->execute();

// Prepare data for the chart
$chartLabels = [];
$chartData = [];
$chartColors = [];

while($row = $smtp->fetch(PDO::FETCH_ASSOC)) {
    // Fetch user details
    $sql2 = "SELECT * FROM users WHERE userMail = :userMail ";
    $smtp2 = $pdo->prepare($sql2);
    $smtp2->execute([':userMail' => $row['CreateBy']]);
    $row2 = $smtp2->fetch(PDO::FETCH_ASSOC);
    
    if ($row2) {
        $chartLabels[] = htmlspecialchars($row2['userName']);
        $chartData[] = (int)$row['total_records'];
    }
}

// Convert PHP arrays to JavaScript arrays
$jsLabels = json_encode($chartLabels);
$jsData = json_encode($chartData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ExpoFlyer Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .chart-title {
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 600;
        }
        
        /* Tabs for chart types */
        .chart-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .chart-tab {
            padding: 8px 16px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .chart-tab:first-child {
            border-radius: 4px 0 0 4px;
        }
        
        .chart-tab:last-child {
            border-radius: 0 4px 4px 0;
        }
        
        .chart-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Summary cards */
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            margin-bottom: 20px;
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .summary-label {
            color: #6c757d;
            font-weight: 500;
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
            <li class="active">
                <a href="./Analitics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
            </li>
            <li>
                <a href="./AllCus.php"><i class="fas fa-user"></i>All Customers</a>
            </li>

            <li>
                <a href="./AddUser.php"><i class="fas fa-users"></i>Add Users</a>
            </li>
            <li>
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
            <div class="user-avatar"><?php echo substr($userName, 0, 1); ?></div>
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
            
            <h2 class="section-title">Analytics Overview</h2>
            
            <!-- Summary Cards -->
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card dashboard-card card-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h5 class="stat-number"><?php echo array_sum($chartData); ?></h5>
                                    <p class="text-muted mb-0">Total Customers</p>
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
                                    <h5 class="stat-number"><?php echo count($chartData); ?></h5>
                                    <p class="text-muted mb-0">Active Users</p>
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
                                    <h5 class="stat-number"><?php echo max($chartData); ?></h5>
                                    <p class="text-muted mb-0">Most by One User</p>
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
                                    <h5 class="stat-number"><?php echo round(array_sum($chartData) / count($chartData), 1); ?></h5>
                                    <p class="text-muted mb-0">Average per User</p>
                                </div>
                                <div class="col-4 text-end">
                                    <i class="fas fa-clock stat-icon text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Customer Creation Analytics</h5>
                            <div class="chart-tabs">
                                <div class="chart-tab active" data-chart-type="bar">Bar</div>
                                <div class="chart-tab" data-chart-type="line">Line</div>
                                <div class="chart-tab" data-chart-type="pie">Pie</div>
                                <div class="chart-tab" data-chart-type="doughnut">Doughnut</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="userChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Creation Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Number of Customers Created by User</h5>
                            <button class="btn btn-outline-primary btn-sm">Export Data</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>User Name</th>
                                            <th>User Email</th>
                                            <th>Phone</th>
                                            <th>Customers Created</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Reset the pointer for the result set
                                        $smtp->execute();
                                        while($row = $smtp->fetch(PDO::FETCH_ASSOC)) {
                                            $sql2 = "SELECT * FROM users WHERE userMail = :userMail ";
                                            $smtp2 = $pdo->prepare($sql2);
                                            $smtp2->execute([':userMail' => $row['CreateBy']]);
                                            $row2 = $smtp2->fetch(PDO::FETCH_ASSOC);
                                            if ($row2) {
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row2['userName']); ?></td>
                                                <td><?= htmlspecialchars($row2['userMail']); ?></td>
                                                <td><?= htmlspecialchars($row2['phone']); ?></td>
                                                <td><?= htmlspecialchars($row['total_records']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                                </td>
                                            </tr>
                                        <?php } } ?>
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
            
            // Initialize the chart
            const ctx = document.getElementById('userChart').getContext('2d');
            let userChart;
            
            // Function to create chart with specified type
            function createChart(type) {
                if (userChart) {
                    userChart.destroy();
                }
                
                const primaryColor = '#b42020';
                const secondaryColor = '#2036b4';
                
                // Data for the chart
                const data = {
                    labels: <?php echo $jsLabels; ?>,
                    datasets: [{
                        label: 'Customers Created',
                        data: <?php echo $jsData; ?>,
                        backgroundColor: type === 'pie' || type === 'doughnut' ? 
                            [primaryColor, secondaryColor, '#20b46c', '#b4a420', '#7c20b4', '#b4207c', '#20a4b4'] :
                            primaryColor,
                        borderColor: type === 'pie' || type === 'doughnut' ? 
                            '#ffffff' : 
                            secondaryColor,
                        borderWidth: 1
                    }]
                };
                
                // Options for the chart
                const options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Customers'
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Users'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: type === 'pie' || type === 'doughnut' ? 'right' : 'top'
                        },
                        title: {
                            display: true,
                            text: 'Customer Creation by User'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Customers: ${context.raw}`;
                                }
                            }
                        }
                    }
                };
                
                // Create the chart
                userChart = new Chart(ctx, {
                    type: type,
                    data: data,
                    options: options
                });
            }
            
            // Create initial bar chart
            createChart('bar');
            
            // Add event listeners to chart type tabs
            const chartTabs = document.querySelectorAll('.chart-tab');
            chartTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    chartTabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Create chart with selected type
                    const chartType = this.getAttribute('data-chart-type');
                    createChart(chartType);
                });
            });
        });
    </script>
</body>
</html>