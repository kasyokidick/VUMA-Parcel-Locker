<?php
// VUMA Parcel Locker System - Front Controller
// This routes all requests to the correct pages

session_start();

// Get the requested path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Clean up the path
$path = str_replace('/VUMA-Parcel-Locker-', '', $path); // Remove repo prefix
$path = ltrim($path, '/');
$path = strtok($path, '?'); // Remove query parameters
$path = rtrim($path, '/');

// Route to appropriate page
switch ($path) {
    case '':
    case 'index':
    case 'index.php':
        // Look for the home page
        if (file_exists('index-home.php')) {
            require_once 'index-home.php';
        } elseif (file_exists('home.php')) {
            require_once 'home.php';
        } else {
            // Create a simple home page if none exists
            echo '<!DOCTYPE html>
<html>
<head>
    <title>VUMA Parcel Locker Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center">
                    <div class="card-body p-5">
                        <h1 class="display-4 text-primary mb-4">VUMA Parcel Lockers</h1>
                        <p class="lead mb-4">Smart Parcel Management System</p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="login" class="btn btn-primary btn-lg me-md-2">Login</a>
                            <a href="setup" class="btn btn-outline-secondary btn-lg">Database Setup</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
        }
        break;

    case 'setup':
        if (file_exists('setup.php')) {
            require_once 'setup.php';
        } else {
            http_response_code(404);
            echo '<h1>Setup page not found</h1>';
        }
        break;

    case 'login':
        if (file_exists('login.php')) {
            require_once 'login.php';
        } else {
            http_response_code(404);
            echo '<h1>Login page not found</h1>';
        }
        break;

    case 'dashboard':
        if (file_exists('dashboard-user.php')) {
            require_once 'dashboard-user.php';
        } else {
            http_response_code(404);
            echo '<h1>Dashboard not found</h1>';
        }
        break;

    case 'admin':
        if (file_exists('dashboard-admin.php')) {
            require_once 'dashboard-admin.php';
        } else {
            http_response_code(404);
            echo '<h1>Admin dashboard not found</h1>';
        }
        break;

    case 'parcels':
        if (file_exists('parcels.php')) {
            require_once 'parcels.php';
        } else {
            http_response_code(404);
            echo '<h1>Parcels page not found</h1>';
        }
        break;

    case 'lockers':
        if (file_exists('lockers.php')) {
            require_once 'lockers.php';
        } else {
            http_response_code(404);
            echo '<h1>Lockers page not found</h1>';
        }
        break;

    // Handle API routes
    default:
        if (strpos($path, 'api/') === 0) {
            $api_path = $path . '.php';
            if (file_exists($api_path)) {
                require_once $api_path;
            } else {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'API endpoint not found']);
            }
        } else {
            // Check if there's a direct PHP file
            if (file_exists($path . '.php')) {
                require_once $path . '.php';
            } else {
                http_response_code(404);
                echo '<!DOCTYPE html>
<html>
<head>
    <title>Page Not Found - VUMA Parcel Locker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body p-5">
                        <h1 class="display-1 text-primary">404</h1>
                        <h3 class="mb-3">Page Not Found</h3>
                        <p class="text-muted mb-4">The page you are looking for does not exist.</p>
                        <a href="/" class="btn btn-primary">Go Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
            }
        }
        break;
}
?>
