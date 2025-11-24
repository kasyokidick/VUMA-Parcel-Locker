<?php
/**
 * User Dashboard Data API
 * Fetches user-specific dashboard statistics and parcel data
 */

header('Content-Type: application/json');

require_once '../auth/session-check.php';
require_once '../config/database.php';

if (isAdmin()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'This endpoint is for customer users only'
    ]);
    exit();
}

$userEmail = $_SESSION['email'];

try {
    // Get user statistics
    $sql = "SELECT
                COUNT(CASE WHEN status = 'ready_for_pickup' THEN 1 END) as waiting,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered,
                COUNT(*) as total
            FROM parcels
            WHERE recipient_email = ?";

    $stmt = executeQuery($conn, $sql, [$userEmail]);
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    // Get recent parcels
    $sql = "SELECT p.id, p.tracking_number, p.status, p.created_at,
                   l.locker_number, l.location
            FROM parcels p
            LEFT JOIN lockers l ON p.locker_id = l.id
            WHERE p.recipient_email = ?
            ORDER BY p.created_at DESC
            LIMIT 5";

    $stmt = executeQuery($conn, $sql, [$userEmail]);
    $result = $stmt->get_result();

    $parcels = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_date'] = date('M j, Y', strtotime($row['created_at']));
        $row['created_time'] = date('g:i A', strtotime($row['created_at']));
        $row['locker_info'] = $row['locker_number'] ? $row['locker_number'] . ' (' . $row['location'] . ')' : 'Not assigned';
        $parcels[] = $row;
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ],
        'stats' => [
            'waiting' => (int)$stats['waiting'],
            'delivered' => (int)$stats['delivered'],
            'total' => (int)$stats['total']
        ],
        'parcels' => $parcels
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load dashboard data'
    ]);
}

$conn->close();
?>