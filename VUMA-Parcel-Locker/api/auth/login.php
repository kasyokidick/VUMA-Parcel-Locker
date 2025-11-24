<?php
header('Content-Type: application/json');
require_once '../config/database.php';

session_start();

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Please enter email and password']);
    exit();
}

try {
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = executeQuery($conn, $sql, [$email]);
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        exit();
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        exit();
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    $redirectPage = ($user['role'] === 'admin') ? 'dashboard-admin.php' : 'dashboard-user.php';

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => $redirectPage,
        'user' => [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Login failed']);
}
?>