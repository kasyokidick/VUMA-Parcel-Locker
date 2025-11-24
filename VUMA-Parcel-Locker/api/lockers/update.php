<?php
/**
 * Locker Update API
 * Updates locker status and information
 * Admin only functionality
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

$input = json_decode(file_get_contents('php://input'), true);
$lockerId = $input['locker_id'] ?? '';
$newStatus = $input['status'] ?? '';

if (empty($lockerId)) {
    echo json_encode([
        'success' => false,
        'error' => 'Locker ID is required'
    ]);
    exit();
}

$validStatuses = ['available', 'occupied', 'maintenance'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid status. Valid statuses: ' . implode(', ', $validStatuses)
    ]);
    exit();
}

try {
    $conn->begin_transaction();

    // Get current locker info
    $sql = "SELECT id, locker_number, status, location FROM lockers WHERE id = ?";
    $stmt = executeQuery($conn, $sql, [$lockerId]);
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Locker not found'
        ]);
        $conn->rollback();
        exit();
    }

    $currentLocker = $result->fetch_assoc();

    // Update locker
    $sql = "UPDATE lockers SET status = ? WHERE id = ?";
    $stmt = executeQuery($conn, $sql, [$newStatus, $lockerId]);

    if ($stmt->affected_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'No changes made to locker'
        ]);
        $conn->rollback();
        exit();
    }

    error_log("Locker updated: ID {$lockerId} ({$currentLocker['locker_number']}) - status: {$currentLocker['status']} → {$newStatus} by admin {$_SESSION['email']}");

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Locker updated successfully',
        'previous_status' => $currentLocker['status'],
        'new_status' => $newStatus
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update locker. Please try again later.'
    ]);
}

$conn->close();
?>