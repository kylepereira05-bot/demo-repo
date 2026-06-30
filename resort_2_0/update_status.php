<?php
session_start();
require 'db.php';

// Security: Only logged-in Staff/Admin can process check-outs
if (!isset($_SESSION['staff_id'])) {
    header("Location: login_page.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $res_id = $_GET['id'];
    $action = $_GET['action'];
    $staff_id = $_SESSION['staff_id']; // The ID of the staff performing the check-out

    try {
        $pdo->beginTransaction();

        if ($action === 'checkout') {
            // 1. Fetch reservation details to get the price, room_id, and payment_method
            $stmt = $pdo->prepare("SELECT r.room_id, r.payment_method, a.price_per_night 
                                   FROM reservations r 
                                   JOIN accommodations a ON r.room_id = a.room_id 
                                   WHERE r.res_id = ?");
            $stmt->execute([$res_id]);
            $data = $stmt->fetch();

            if ($data) {
                // 2. Update Reservation Status to 'Completed'
                $updateRes = $pdo->prepare("UPDATE reservations SET status = 'Completed' WHERE res_id = ?");
                $updateRes->execute([$res_id]);

                // 3. Update Room Status back to 'Available'
                $updateRoom = $pdo->prepare("UPDATE accommodations SET status = 'Available' WHERE room_id = ?");
                $updateRoom->execute([$data['room_id']]);

                // 4. Record Revenue in the payments table using the actual payment_method
                $insertPayment = $pdo->prepare("INSERT INTO payments (res_id, staff_id, amount, payment_method, payment_status, transaction_date) 
                                               VALUES (?, ?, ?, ?, 'Paid', NOW())");
                $insertPayment->execute([
                    $res_id, 
                    $staff_id, 
                    $data['price_per_night'],
                    $data['payment_method'] // Dynamically uses 'Online' or 'Walk-in' from the reservation
                ]);

                $msg = "Check-out completed and revenue recorded successfully.";
            } else {
                $msg = "Error: Reservation data not found.";
            }
        } 
        elseif ($action === 'approve') {
            // Logic for approving a pending reservation
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'Approved' WHERE res_id = ?");
            $stmt->execute([$res_id]);
            $msg = "Reservation has been approved.";
        }
        elseif ($action === 'reject') {
            // Logic for rejecting a reservation
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'Rejected' WHERE res_id = ?");
            $stmt->execute([$res_id]);
            $msg = "Reservation has been rejected.";
        }

        $pdo->commit();
        header("Location: manage_reservations.php?msg=" . urlencode($msg));
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("System Error: " . $e->getMessage());
    }
} else {
    header("Location: manage_reservations.php");
    exit();
}