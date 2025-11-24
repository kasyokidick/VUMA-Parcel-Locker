<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VUMA Parcel Lockers - Modern Parcel Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 100px 0;
        }
        .logo {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .feature-card {
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-box-seam me-2"></i>VUMA
            </a>
            <div class="navbar-nav ms-auto">
                <a href="login.php" class="btn btn-light">Login</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="logo">
                        <i class="bi bi-box-seam"></i> VUMA
                    </div>
                    <h1 class="display-4 fw-bold mb-4">Parcel Locker Management System</h1>
                    <p class="lead mb-4">
                        Modern, secure, and efficient parcel management solution for businesses and customers.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="login.php" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Key Features</h2>
                <p class="lead">Everything you need for modern parcel management</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-check" style="font-size: 3rem; color: #3b82f6;"></i>
                        </div>
                        <h4>Secure Access</h4>
                        <p>OTP-based pickup verification ensures only authorized users can access their parcels.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <i class="bi bi-phone" style="font-size: 3rem; color: #3b82f6;"></i>
                        </div>
                        <h4>Mobile Friendly</h4>
                        <p>Access your parcels and generate OTP codes from any device, anywhere.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="text-center mb-4">
                            <i class="bi bi-truck" style="font-size: 3rem; color: #3b82f6;"></i>
                        </div>
                        <h4>Real-time Tracking</h4>
                        <p>Track your parcels from delivery to pickup with live status updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-box-seam me-2"></i>VUMA Parcel Lockers</h5>
                    <p class="text-muted">Modern parcel management solution for businesses and customers.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        &copy; 2024 VUMA Parcel Lockers. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/functions.js"></script>
</body>
</html>
