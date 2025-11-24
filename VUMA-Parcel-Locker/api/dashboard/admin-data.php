<?php
/**
 * Admin Dashboard Data API
 * Fetches system-wide statistics for administrators
 */

header('Content-Type: application/json');

require_once '../auth/session-check.php';
require_once '../config/database.php';

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied. Admin privileges required.'
    ]);
    exit();
}

try {
    // Get system statistics
    $stats = [];
    
    // Total users
    $sql = "SELECT COUNT(*) as total_users FROM users";
    $stmt = executeQuery($conn, $sql);
    $result = $stmt->get_result();
    $stats['total_users'] = $result->fetch_assoc()['total_users'];

    // Total lockers and status breakdown
    $sql = "SELECT
                COUNT(*) as total_lockers,
                COUNT(CASE WHEN status = 'available' THEN 1 END) as available,
                COUNT(CASE WHEN status = 'occupied' THEN 1 END) as occupied,
                COUNT(CASE WHEN status = 'maintenance' THEN 1 END) as maintenance
            FROM lockers";
    $stmt = executeQuery($conn, $sql);
    $result = $stmt->get_result();
    $lockerStats = $result->fetch_assoc();
    $stats['lockers'] = [
        'total' => (int)$lockerStats['total_lockers'],
        'available' => (int)$lockerStats['available'],
        'occupied' => (int)$lockerStats['occupied'],
        'maintenance' => (int)$lockerStats['maintenance']
    ];

    // Total parcels and status breakdown
    $sql = "SELECT
                COUNT(*) as total_parcels,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'in_transit' THEN 1 END) as in_transit,
                COUNT(CASE WHEN status = 'ready_for_pickup' THEN 1 END) as ready_for_pickup,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered
            FROM parcels";
    $stmt = executeQuery($conn, $sql);
    $result = $stmt->get_result();
    $parcelStats = $result->fetch_assoc();
    $stats['parcels'] = [
        'total' => (int)$parcelStats['total_parcels'],
        'pending' => (int)$parcelStats['pending'],
        'in_transit' => (int)$parcelStats['in_transit'],
        'ready_for_pickup' => (int)$parcelStats['ready_for_pickup'],
        'delivered' => (int)$parcelStats['delivered']
    ];

    echo json_encode([
        'success' => true,
        'user' => [
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ],
        'stats' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load admin dashboard data'
    ]);
}

$conn->close();
?>