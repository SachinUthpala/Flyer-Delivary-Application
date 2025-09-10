<?php session_start(); ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Exhibition Flyer Distribution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #b42020;
            --secondary: #2036b4;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #e6e9ff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .login-container {
            background-color: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin: 0 auto;
            max-width: 450px;
            width: 100%;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary);
        }
        
        .login-logo i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .login-logo h2 {
            font-weight: 700;
            margin: 0;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: #7c1212;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(180, 32, 32, 0.25);
        }
        
        .login-divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }
        
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .login-divider span {
            padding: 0 1rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .social-btn {
            flex: 1;
            padding: 0.6rem;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .facebook-btn {
            background-color: #3b5998;
            color: white;
        }
        
        .google-btn {
            background-color: #db4a39;
            color: white;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .success-alert {
            display: none;
            border-radius: 12px;
            border-left: 5px solid var(--secondary);
        }
        
        /* Mobile responsiveness */
        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem;
                margin: 0 1rem;
            }
            
            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container">
                    <div class="login-logo">
                        <i class="fas fa-file-pdf"></i>
                        <h2>ExpoFlyer Delivery</h2>
                        <p class="text-muted">Access your account</p>
                    </div>
                    
                    <div class="alert alert-success success-alert" id="successAlert" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Login Successful!</h4>
                        <p>You are being redirected to your dashboard.</p>
                    </div>
                    
                    <form id="loginForm" action="Backend/login.php" method="POST">
                        <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']); 
        ?>
    </div>
<?php endif; ?>
    <div class="mb-3">
        <label for="email" class="form-label">Email Address *</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="text" name="emailOrPhone" class="form-control" id="email" placeholder="name@example.com" required>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">Password *</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" name="userPassword" class="form-control" id="password" placeholder="Enter your password" required>
        </div>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="rememberMe">
        <label class="form-check-label" for="rememberMe">Remember me</label>
        <a href="#" class="float-end">Forgot password?</a>
    </div>
    
    <button type="submit" class="btn btn-primary py-3" name="loginUser">
        <i class="fas fa-sign-in-alt me-2"></i>Login to Account
    </button>
</form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>