<?php

$user = $_GET['userName'];
$userPhone = $_GET['userPhone'];
$userEmail = $_GET['userEmail'];

if(!isset($user) || !isset($userPhone)){
    header("Location: ./signIn.php");
}




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibition Flyer Distribution</title>
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
        
        .form-container {
            background-color: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .flyer-preview {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .flyer-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s;
            height: 100%;
            margin-bottom: 1.5rem;
        }
        
        .flyer-card:hover {
            transform: translateY(-5px);
        }
        
        .flyer-img {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #7c1212;
        }
        
        .btn-success {
            background: var(--secondary);
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
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
        
        .success-alert {
            display: none;
            border-radius: 12px;
            border-left: 5px solid var(--secondary);
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
        
        .flyer-checkbox {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            transform: scale(1.5);
        }
        
        .flyer-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #b420206b;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .flyer-card:hover .flyer-overlay {
            opacity: 1;
        }
        
        .email-highlight {
            color: #ffcc00;
            font-weight: bold;
        }
        
        /* Mobile-specific styles */
        @media (max-width: 992px) {
            .flyer-section {
                order: 3;
                margin-top: 1.5rem;
            }
            
            .submit-section {
                order: 4;
                margin-top: 1.5rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .flyer-preview {
                padding: 1rem;
            }
            
            .flyer-card {
                margin-bottom: 1rem;
            }
            
            .flyer-img {
                height: 220px;
            }
        }
        
        /* Custom flyer grid */
        .flyers-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        
        @media (max-width: 992px) {
            .flyers-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Form Section -->
            <div class="col-12">
                <div class="form-container">
                    <h2 class="text-center mb-4">Submit Your Details</h2>
                    
                    <div class="alert alert-success success-alert" id="successAlert" role="alert">
                        <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Success!</h4>
                        <p>Your form has been submitted successfully. The flyers will be sent to your email shortly.</p>
                        <hr>
                        <p class="mb-0">Thank you for visiting our exhibition booth!</p>
                    </div>
                    
                    <form id="flyerDeliveryForm">
                        <input type="hidden" name="createdEmail" value="<?php echo $userEmail; ?>" id="createdBy">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">WhatsApp Number *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                    <input type="tel" class="form-control" id="phone" placeholder="e.g. 0123456789" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="message" class="form-label">Your Message *</label>
                            <textarea class="form-control" id="message" rows="5" placeholder="Type your message here..." required></textarea>
                        </div>

                        
                        
                        
                        <!-- Flyer Selection Inside Form -->
                        <div class="flyer-section">
                            <h3 class="section-title">Available Flyers</h3>
                            <p class="form-text mb-3">Select one or more flyers by clicking on the cards below</p>
                            
                            <div class="flyers-grid">
                                <div class="flyer-card card position-relative">
                                    <input type="checkbox" class="flyer-checkbox form-check-input" id="flyer1" value="pdf1">
                                    <img src="https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Product Catalog">
                                    <div class="flyer-overlay">
                                        <span class="text-white fw-bold">Click to select</span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Product Catalog</h6>
                                        <p class="card-text small">12 pages</p>
                                    </div>
                                </div>
                                <div class="flyer-card card position-relative">
                                    <input type="checkbox" class="flyer-checkbox form-check-input" id="flyer2" value="pdf2">
                                    <img src="https://images.unsplash.com/photo-1588666309990-d68f08e3d4a6?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Product Brochure">
                                    <div class="flyer-overlay">
                                        <span class="text-white fw-bold">Click to select</span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Product Brochure</h6>
                                        <p class="card-text small">4 pages</p>
                                    </div>
                                </div>

                                <div class="flyer-card card position-relative">
                                    <input type="checkbox" class="flyer-checkbox form-check-input" id="flyer3" value="pdf3">
                                    <img src="https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwa90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Price List">
                                    <div class="flyer-overlay">
                                        <span class="text-white fw-bold">Click to select</span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Price List</h6>
                                        <p class="card-text small">2 pages</p>
                                    </div>
                                </div>

                                <div class="flyer-card card position-relative">
                                    <input type="checkbox" class="flyer-checkbox form-check-input" id="flyer4" value="pdf4">
                                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80" class="flyer-img card-img-top" alt="Special Offers">
                                    <div class="flyer-overlay">
                                        <span class="text-white fw-bold">Click to select</span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title">Special Offers</h6>
                                        <p class="card-text small">Limited time</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4 mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">I agree to receive marketing materials</label>
                            </div>
                        </div>
                        
                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-paper-plane me-2"></i>Submit & Receive Flyers via Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- How It Works -->
        <section class="how-it-works">
            <h2 class="text-center mb-5">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="feature-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <h4>1. Fill the Form</h4>
                        <p>Provide your name and email along with your document preference.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="feature-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>2. Submit Details</h4>
                        <p>Click the submit button to process your request for PDF documents.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <h4>3. Receive Documents</h4>
                        <p>Get your PDF files delivered to your email instantly.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make flyer cards clickable
            document.querySelectorAll('.flyer-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('form-check-input')) {
                        const checkbox = this.querySelector('input[type="checkbox"]');
                        checkbox.checked = !checkbox.checked;
                        
                        // Visual feedback for selection
                        if (checkbox.checked) {
                            this.style.boxShadow = "0 0 15px rgba(180, 32, 32, 0.5)";
                        } else {
                            this.style.boxShadow = "";
                        }
                    }
                });
            });
            
            // Form submission
            document.getElementById('flyerDeliveryForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get form values
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                const company = document.getElementById('company').value;
               
                const createdBy = document.getElementById('createdBy').value;
                
                // Get selected flyers
                const selectedFlyers = [];
                document.querySelectorAll('.flyer-checkbox:checked').forEach(checkbox => {
                    selectedFlyers.push(checkbox.value);
                });
                
                if (selectedFlyers.length === 0) {
                    alert('Please select at least one flyer to receive.');
                    return;
                }
                
                // Create FormData object
                const formData = new FormData();
                formData.append('name', name);
                formData.append('email', email);
                formData.append('phone', phone);
                formData.append('company', company);
                formData.append('createdBy', createdBy);
            
                formData.append('flyers', JSON.stringify(selectedFlyers));
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                submitBtn.disabled = true;
                
                // Send data to server
                fetch('send_files.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        document.getElementById('successAlert').style.display = 'block';
                        
                        // Scroll to success message
                        document.getElementById('successAlert').scrollIntoView({ behavior: 'smooth' });
                        
                        // Reset form
                        document.getElementById('flyerDeliveryForm').reset();
                        
                        // Uncheck all flyers
                        document.querySelectorAll('.flyer-checkbox').forEach(checkbox => {
                            checkbox.checked = false;
                            // Remove visual selection
                            checkbox.closest('.flyer-card').style.boxShadow = "";
                        });
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                })
                .finally(() => {
                    // Restore button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>

</html>





