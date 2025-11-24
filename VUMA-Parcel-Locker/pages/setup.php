<?php
/**
 * Database Setup Page
 * Creates all required tables and inserts sample data
 * Run this once to initialize the system
 */

// Include database configuration
require_once '../api/config/database.php';

$success = false;
$error = '';
$setupComplete = false;

// Check if setup already done
function isSetupComplete($conn) {
    $tables = ['users', 'lockers', 'parcels'];
    foreach ($tables as $table) {
        if (!tableExists($conn, $table)) {
            return false;
        }
    }

    // Check if sample data exists
    $result = $conn->query("SELECT COUNT(*) as user_count FROM users");
    $row = $result->fetch_assoc();
    return $row['user_count'] > 0;
}

// Handle setup request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$conn->query($sql)) {
            throw new Exception("Failed to create users table: " . $conn->error);
        }

        // Create lockers table
        $sql = "CREATE TABLE IF NOT EXISTS lockers (
            id INT PRIMARY KEY AUTO_INCREMENT,
            locker_number VARCHAR(10) UNIQUE NOT NULL,
            status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
            location VARCHAR(100) DEFAULT 'Main Branch',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$conn->query($sql)) {
            throw new Exception("Failed to create lockers table: " . $conn->error);
        }

        // Create parcels table
        $sql = "CREATE TABLE IF NOT EXISTS parcels (
            id INT PRIMARY KEY AUTO_INCREMENT,
            tracking_number VARCHAR(20) UNIQUE NOT NULL,
            recipient_name VARCHAR(100) NOT NULL,
            recipient_email VARCHAR(100) NOT NULL,
            locker_id INT,
            status ENUM('pending', 'in_transit', 'ready_for_pickup', 'delivered') DEFAULT 'pending',
            otp_code VARCHAR(6),
            otp_expiry TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (locker_id) REFERENCES lockers(id) ON DELETE SET NULL
        )";

        if (!$conn->query($sql)) {
            throw new Exception("Failed to create parcels table: " . $conn->error);
        }

        // Insert sample users
        $adminPassword = password_hash('password123', PASSWORD_DEFAULT);
        $userPassword = password_hash('password123', PASSWORD_DEFAULT);

        $sql = "INSERT IGNORE INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin'), (?, ?, ?, 'user')";
        $params = ['Admin User', 'admin@vuma.com', $adminPassword, 'John Customer', 'customer@example.com', $userPassword];
        executeQuery($conn, $sql, $params, 'sssss');

        // Insert sample lockers
        $sql = "INSERT IGNORE INTO lockers (locker_number, status, location) VALUES
                ('A001', 'available', 'Main Branch'),
                ('A002', 'occupied', 'Main Branch'),
                ('B001', 'available', 'Main Branch'),
                ('B002', 'maintenance', 'Main Branch'),
                ('C001', 'occupied', 'Main Branch')";

        if (!$conn->query($sql)) {
            throw new Exception("Failed to insert sample lockers: " . $conn->error);
        }

        // Get user ID for sample parcels
        $result = $conn->query("SELECT id FROM users WHERE email = 'customer@example.com' LIMIT 1");
        $user = $result->fetch_assoc();

        if ($user) {
            $userId = $user['id'];

            // Insert sample parcels
            $sql = "INSERT IGNORE INTO parcels (tracking_number, recipient_name, recipient_email, locker_id, status) VALUES
                    ('TRK001', 'John Customer', 'customer@example.com', 2, 'ready_for_pickup'),
                    ('TRK002', 'John Customer', 'customer@example.com', 5, 'in_transit'),
                    ('TRK003', 'John Customer', 'customer@example.com', NULL, 'delivered'),
                    ('TRK004', 'John Customer', 'customer@example.com', 3, 'pending')";

            if (!$conn->query($sql)) {
                throw new Exception("Failed to insert sample parcels: " . $conn->error);
            }
        }

        // Commit transaction
        $conn->commit();
        $success = true;
        $setupComplete = isSetupComplete($conn);

    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Check current setup status
$setupComplete = isSetupComplete($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VUMA Parcel Lockers - Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .setup-card {
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-radius: 15px;
            border: none;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card setup-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="logo">VUMA Parcel Lockers</div>
                            <h2>Database Setup</h2>
                            <p class="text-muted">Initialize your parcel locker management system</p>
                        </div>

                        <?php if ($setupComplete): ?>
                            <div class="alert alert-success">
                                <h4 class="alert-heading">
                                    <i class="bi bi-check-circle-fill"></i> Setup Complete!
                                </h4>
                                <p>Your VUMA Parcel Locker system has been successfully set up with sample data.</p>
                                <hr>
                                <p class="mb-0">
                                    <strong>Demo Accounts:</strong><br>
                                    Admin: admin@vuma.com / password123<br>
                                    Customer: customer@example.com / password123
                                </p>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-house"></i> Go to Homepage
                                </a>
                            </div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success">
                                <h4 class="alert-heading">
                                    <i class="bi bi-check-circle-fill"></i> Setup Successful!
                                </h4>
                                <p>Database tables and sample data have been created successfully.</p>
                                <p class="mb-0">
                                    <strong>Login Credentials:</strong><br>
                                    Admin: admin@vuma.com / password123<br>
                                    Customer: customer@example.com / password123
                                </p>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-house"></i> Go to Homepage
                                </a>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger">
                                <h4 class="alert-heading">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Setup Failed!
                                </h4>
                                <p><?php echo htmlspecialchars($error); ?></p>
                                <p class="mb-0">Please check your database configuration and try again.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h4 class="alert-heading">
                                    <i class="bi bi-info-circle-fill"></i> Setup Required
                                </h4>
                                <p>This setup will create all necessary database tables and insert sample data for demonstration.</p>
                                <hr>
                                <p class="mb-0">
                                    <strong>This will create:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Users table with admin and customer accounts</li>
                                        <li>Lockers table with sample locker locations</li>
                                        <li>Parcels table with sample parcel data</li>
                                    </ul>
                                </p>
                            </div>
                        <?php endif; ?>

                        <?php if (!$setupComplete && !$success): ?>
                            <form method="POST" class="mt-4">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-gear"></i> Initialize Database
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>