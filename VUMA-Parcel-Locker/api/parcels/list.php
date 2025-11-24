<?php
/**
 * Parcels List API
 * Fetches parcel data for users and administrators
 */

header('Content-Type: application/json');

require_once '../auth/session-check.php';
require_once '../config/database.php';

$userEmail = $_SESSION['email'];
$isAdmin = isAdmin();

try {
    // Build base query
    if ($isAdmin) {
        $sql = "SELECT p.id, p.tracking_number, p.recipient_name, p.recipient_email,
                      p.status, p.otp_code, p.otp_expiry, p.created_at,
                      l.locker_number, l.location
                FROM parcels p
                LEFT JOIN lockers l ON p.locker_id = l.id
                ORDER BY p.created_at DESC
                LIMIT 10";
        $params = [];
    } else {
        $sql = "SELECT p.id, p.tracking_number, p.recipient_name, p.recipient_email,
                      p.status, p.otp_code, p.otp_expiry, p.created_at,
                      l.locker_number, l.location
                FROM parcels p
                LEFT JOIN lockers l ON p.locker_id = l.id
                WHERE p.recipient_email = ?
                ORDER BY p.created_at DESC
                LIMIT 10";
        $params = [$userEmail];
    }

    $stmt = executeQuery($conn, $sql, $params);
    $result = $stmt->get_result();

    $parcels = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_date'] = date('M j, Y', strtotime($row['created_at']));
        $row['created_time'] = date('g:i A', strtotime($row['created_at']));
        
        $row['otp_active'] = !empty($row['otp_code']) &&
                            $row['otp_expiry'] &&
                            strtotime($row['otp_expiry']) > time();

        if ($row['otp_active']) {
            $row['otp_expiry_time'] = date('g:i A', strtotime($row['otp_expiry']));
        }

        $row['locker_info'] = $row['locker_number'] 
            ? $row['locker_number'] . ' (' . $row['location'] . ')' 
            : 'Not assigned';

        $row['status_display'] = getParcelStatusDisplay($row['status']);
        $row['status_badge'] = getParcelStatusBadge($row['status']);

        $parcels[] = $row;
    }

    echo json_encode([
        'success' => true,
        'parcels' => $parcels,
        'user' => [
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'is_admin' => $isAdmin
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load parcel data'
    ]);
}

$conn->close();

function getParcelStatusDisplay($status) {
    $statuses = [
        'pending' => 'Pending',
        'in_transit' => 'In Transit',
        'ready_for_pickup' => 'Ready for Pickup',
        'delivered' => 'Delivered'
    ];
    return $statuses[$status] ?? 'Unknown';
}

function getParcelStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Pending</span>',
        'in_transit' => '<span class="badge bg-primary"><i class="bi bi-truck me-1"></i>In Transit</span>',
        'ready_for_pickup' => '<span class="badge bg-warning"><i class="bi bi-clock-history me-1"></i>Ready</span>',
        'delivered' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Delivered</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>