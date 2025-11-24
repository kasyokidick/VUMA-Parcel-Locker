<?php
/**
 * Logout Handler
 * Securely destroys user session and redirects to login
 */

// Start session
session_start();

// Log user logout for security monitoring
if (isset($_SESSION['email'])) {
    error_log("User logged out: " . $_SESSION['email'] . " from IP: " . $_SERVER['REMOTE_ADDR']);
}

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Return JSON response for AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect' => 'login.php'
    ]);
    exit();
}

// Redirect to login page for regular requests
header('Location: login.php');
exit();
?>