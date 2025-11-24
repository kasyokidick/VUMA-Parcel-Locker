<?php
// VUMA Parcel Locker System - Front Controller
// This file handles all routing and directs to appropriate pages

session_start();

// Get the requested path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/VUMA-Parcel-Locker-', '', $path); // Remove repo prefix if present
$path = rtrim($path, '/');

// Route to appropriate page
switch ($path) {
    case '':
    case '/':
        require_once 'pages/index.php';
        break;

    case '/setup':
        require_once 'pages/setup.php';
        break;

    case '/login':
        require_once 'pages/login.php';
        break;

    case '/dashboard':
        require_once 'pages/dashboard-user.php';
        break;

    case '/admin':
        require_once 'pages/dashboard-admin.php';
        break;

    case '/parcels':
        require_once 'pages/parcels.php';
        break;

    case '/lockers':
        require_once 'pages/lockers.php';
        break;

    // Handle API routes
    case str_starts_with($path, '/api/'):
        $api_file = substr($path, 5); // Remove '/api' prefix
        $api_path = 'api/' . $api_file . '.php';

        if (file_exists($api_path)) {
            require_once $api_path;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint not found']);
        }
        break;

    default:
        // 404 - Page not found
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
        break;
}
?>
