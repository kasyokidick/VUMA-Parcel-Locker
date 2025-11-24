<?php
/**
 * OTP Generation API
 * Generates secure 6-digit codes for parcel pickup
 */

header('Content-Type: application/json');

require_once '../auth/session-check.php';
require_once '../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$parcelId = $input['parcel_id'] ?? '';

if (empty($parcelId)) {
    echo json_encode([
        'success' => false,
        'error' => 'Parcel ID is required'
    ]);
    exit();
}

$userEmail = $_SESSION['email'];

try {
    $conn->begin_transaction();

    // Find the parcel - ensure user owns it or admin is requesting
    if (!isAdmin()) {
        $sql = "SELECT id, tracking_number, recipient_email, status, otp_code, otp_expiry
                FROM parcels
                WHERE id = ? AND recipient_email = ?";
        $params = [$parcelId, $userEmail];
    } else {
        $sql = "SELECT id, tracking_number, recipient_email, status, otp_code, otp_expiry
                FROM parcels
                WHERE id = ?";
        $params = [$parcelId];
    }

    $stmt = executeQuery($conn, $sql, $params);
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Parcel not found or access denied'
        ]);
        $conn->rollback();
        exit();
    }

    $parcel = $result->fetch_assoc();

    // Check if parcel is ready for pickup
    if ($parcel['status'] !== 'ready_for_pickup') {
        echo json_encode([
            'success' => false,
            'error' => 'OTP can only be generated for parcels ready for pickup'
        ]);
        $conn->rollback();
        exit();
    }

    // Check if OTP is already active
    if (!empty($parcel['otp_code']) && $parcel['otp_expiry'] && strtotime($parcel['otp_expiry']) > time()) {
        $expiryTime = date('g:i A', strtotime($parcel['otp_expiry']));
        echo json_encode([
            'success' => false,
            'error' => "An active OTP already exists. Current OTP: {$parcel['otp_code']} (Expires: {$expiryTime})"
        ]);
        $conn->rollback();
        exit();
    }

    // Generate new 6-digit OTP
    $otpCode = sprintf('%06d', rand(100000, 999999));
    $otpExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    // Update parcel with new OTP
    $sql = "UPDATE parcels
            SET otp_code = ?, otp_expiry = ?
            WHERE id = ?";

    $stmt = executeQuery($conn, $sql, [$otpCode, $otpExpiry, $parcel['id']]);

    if ($stmt->affected_rows === 0) {
        throw new Exception("Failed to update parcel with OTP");
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'OTP generated successfully',
        'otp_code' => $otpCode,
        'parcel_tracking' => $parcel['tracking_number'],
        'expiry_time' => date('g:i A', strtotime($otpExpiry))
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'error' => 'Failed to generate OTP. Please try again later.'
    ]);
}

$conn->close();
?>