<?php
/**
 * Session Checker
 * Authentication guard for protected pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    // Store requested page for redirect after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

    // Return JSON error for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Session expired. Please log in again.',
            'redirect' => 'login.php'
        ]);
        exit();
    }

    // Redirect to login page for regular requests
    header('Location: ../pages/login.php');
    exit();
}

// Check session timeout (30 minutes)
$sessionTimeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
    session_unset();
    session_destroy();

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Session expired. Please log in again.',
            'redirect' => 'login.php'
        ]);
        exit();
    }

    header('Location: ../pages/login.php?error=expired');
    exit();
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

?>