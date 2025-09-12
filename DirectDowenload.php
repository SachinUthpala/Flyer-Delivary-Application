<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Product Catalogs</title>
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
            padding-bottom: 2rem;
        }
        
        .header {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
        }
        
        .flyer-container {
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .flyer-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .flyer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }
        
        .flyer-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .btn-download {
            background: var(--primary);
            border: none;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            width: 100%;
            border-radius: 8px;
        }
        
        .btn-download:hover {
            background: #9b1b1b;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .how-it-works {
            padding: 3rem 0;
        }
        
        .step-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .step-card:hover {
            transform: translateY(-5px);
        }
        
        .footer {
            background: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .social-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: white;
        }
        
        .section-title {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }
        
        .flyer-info {
            padding: 1.2rem;
        }
        
        .file-details {
            display: flex;
            justify-content: space-between;
            margin-top: 0.8rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Mobile-specific styles */
        @media (max-width: 768px) {
            .flyer-img {
                height: 180px;
            }
            
            .flyer-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Product Catalogs & Brochures</h1>
            <p class="lead">Download our latest product information directly</p>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container">
        <!-- Flyer Gallery -->
        <div class="flyer-container">
            <h2 class="section-title text-center mb-4">Available Downloads</h2>
            <p class="text-center mb-4">Click on any download button to get the PDF directly</p>
            
            <div class="row">
                <!-- Flyer 1 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="flyer-card card">
                        <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Product Catalog">
                        <div class="flyer-info">
                            <h5 class="card-title">Product Catalog</h5>
                            <p class="card-text">Complete product   specifications and features.</p>
                            <div class="file-details">
                                <span><i class="far fa-file-pdf"></i> PDF</span>
                                <span><i class="far fa-clock"></i> 12 pages</span>
                            </div>
                            <a href="Flyer/pdf1.pdf" download class="btn btn-download mt-3">
                                <i class="fas fa-download me-2"></i>Download Now
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Flyer 2 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="flyer-card card">
                        <img src="https://images.unsplash.com/photo-1588666309990-d68f08e3d4a6?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Product Brochure">
                        <div class="flyer-info">
                            <h5 class="card-title">Product Brochure</h5>
                            <p class="card-text">Detailed brochure  our premium products.</p>
                            <div class="file-details">
                                <span><i class="far fa-file-pdf"></i> PDF</span>
                                <span><i class="far fa-clock"></i> 4 pages</span>
                            </div>
                            <a href="Flyer/pdf2.pdf" download class="btn btn-download mt-3">
                                <i class="fas fa-download me-2"></i>Download Now
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Flyer 3 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="flyer-card card">
                        <img src="https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Price List">
                        <div class="flyer-info">
                            <h5 class="card-title">Price List</h5>
                            <p class="card-text">Updated pricing for all our products and services.</p>
                            <div class="file-details">
                                <span><i class="far fa-file-pdf"></i> PDF</span>
                                <span><i class="far fa-clock"></i> 2 pages</span>
                            </div>
                            <a href="Flyer/pdf3.pdf" download class="btn btn-download mt-3">
                                <i class="fas fa-download me-2"></i>Download Now
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Flyer 4 -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="flyer-card card">
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Special Offers">
                        <div class="flyer-info">
                            <h5 class="card-title">Special Offers</h5>
                            <p class="card-text">Limited time promotions and special discounts.</p>
                            <div class="file-details">
                                <span><i class="far fa-file-pdf"></i> PDF</span>
                                <span><i class="far fa-clock"></i> 6 pages</span>
                            </div>
                            <a href="Flyer/pdf4.pdf" download class="btn btn-download mt-3">
                                <i class="fas fa-download me-2"></i>Download Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- How It Works -->
        <section class="how-it-works">
            <h2 class="text-center mb-5">How To Download</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>1. Browse Catalogs</h4>
                        <p>Explore our available product catalogs and brochures.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="feature-icon">
                            <i class="fas fa-mouse-pointer"></i>
                        </div>
                        <h4>2. Click Download</h4>
                        <p>Press the download button on any document you need.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <h4>3. Get Your File</h4>
                        <p>The PDF will download directly to your device.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer text-center mt-5">
        <div class="container">
            <p>&copy; 2023 Company Name. All rights reserved.</p>
            <div class="mt-3">
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to download buttons
        document.querySelectorAll('.btn-download').forEach(button => {
            button.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Downloading...';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-check me-2"></i>Downloaded!';
                    this.classList.add('btn-success');
                    this.classList.remove('btn-download');
                    
                    // Revert after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-download me-2"></i>Download Now';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-download');
                    }, 2000);
                }, 1000);
            });
        });
    </script>
</body>
</html>