<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $redirect = ($_SESSION['role'] === 'admin') ? 'dashboard-admin.php' : 'dashboard-user.php';
    header("Location: $redirect");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VUMA Parcel Lockers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 400px;
            width: 90%;
        }
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #3b82f6;
            text-align: center;
            margin-bottom: 2rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .demo-accounts {
            background: rgba(59, 130, 246, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="bi bi-box-seam"></i> VUMA
        </div>
        <h3 class="text-center mb-4">Welcome Back</h3>
        <p class="text-center text-muted mb-4">Please login to your account</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Session expired. Please login again.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                You have been logged out successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label">
                    <i class="bi bi-envelope me-2"></i>Email Address
                </label>
                <input type="email" class="form-control" id="email" name="email" required
                       placeholder="Enter your email" autocomplete="email">
            </div>
            <div class="mb-4">
                <label class="form-label">
                    <i class="bi bi-lock me-2"></i>Password
                </label>
                <input type="password" class="form-control" id="password" name="password" required
                       placeholder="Enter your password" autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                <span>Login</span>
                <div class="spinner-border spinner-border-sm d-none ms-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </form>

        <div class="demo-accounts">
            <h6 class="mb-3">
                <i class="bi bi-info-circle me-2"></i>Demo Accounts
            </h6>
            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <strong>Admin:</strong><br>
                    admin@vuma.com<br>
                    password123
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <strong>Customer:</strong><br>
                    customer@example.com<br>
                    password123
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
</body>
</html>
