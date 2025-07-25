<?php
session_start();
require 'config.php';

$provider_id = $_SESSION['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $status = $_POST['status'] ?? '';

    $allowed_statuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }

    if ($booking_id <= 0 || $provider_id == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking or provider']);
        exit();
    }

    $updateStmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND provider_id = ?");
    if (!$updateStmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit();
    }

    $updateStmt->bind_param("sii", $status, $booking_id, $provider_id);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No rows updated or permission denied']);
    }
    $updateStmt->close();
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit();
