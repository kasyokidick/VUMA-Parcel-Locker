<?php
/**
 * Lockers List API
 * Fetches locker data for display and management
 */

header('Content-Type: application/json');

require_once '../auth/session-check.php';
require_once '../config/database.php';

$isAdmin = isAdmin();

try {
    if ($isAdmin) {
        $sql = "SELECT l.id, l.locker_number, l.status, l.location, l.created_at,
                       COUNT(p.id) as parcel_count
                FROM lockers l
                LEFT JOIN parcels p ON l.id = p.locker_id
                GROUP BY l.id, l.locker_number, l.status, l.location, l.created_at
                ORDER BY l.locker_number";
        $params = [];
    } else {
        $sql = "SELECT locker_number, status, location
                FROM lockers
                ORDER BY locker_number";
        $params = [];
    }

    $stmt = executeQuery($conn, $sql, $params);
    $result = $stmt->get_result();

    $lockers = [];
    while ($row = $result->fetch_assoc()) {
        $row['status_display'] = getLockerStatusDisplay($row['status']);
        $row['status_badge'] = getLockerStatusBadge($row['status']);
        $row['created_date'] = date('M j, Y', strtotime($row['created_at'] ?? 'now'));

        if ($isAdmin) {
            $row['parcel_count'] = (int)$row['parcel_count'];
            $row['can_edit'] = true;
        }

        $lockers[] = $row;
    }

    echo json_encode([
        'success' => true,
        'lockers' => $lockers,
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
        'error' => 'Failed to load locker data'
    ]);
}

$conn->close();

function getLockerStatusDisplay($status) {
    $statuses = [
        'available' => 'Available',
        'occupied' => 'Occupied',
        'maintenance' => 'Under Maintenance'
    ];
    return $statuses[$status] ?? 'Unknown';
}

function getLockerStatusBadge($status) {
    $badges = [
        'available' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Available</span>',
        'occupied' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Occupied</span>',
        'maintenance' => '<span class="badge bg-warning"><i class="bi bi-tools me-1"></i>Maintenance</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>