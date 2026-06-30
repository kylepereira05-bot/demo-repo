<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $room_id = $_POST['room_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $payment_method = $_POST['payment_method']; // Capture the payment method

    try {
        // --- 1. DOUBLE-BOOKING PREVENTION CHECK ---
        // This query checks if there's any reservation for the same room 
        // that overlaps with the selected dates and is NOT 'Cancelled' or 'Rejected'
        $check_sql = "SELECT COUNT(*) FROM reservations 
                      WHERE room_id = ? 
                      AND status NOT IN ('Cancelled', 'Rejected') 
                      AND (
                          (check_in <= ? AND check_out >= ?)
                      )";
        
        $check_stmt = $pdo->prepare($check_sql);
        // We check if the existing booking's stay period overlaps with the new request
        $check_stmt->execute([$room_id, $check_out, $check_in]);
        $conflict_count = $check_stmt->fetchColumn();

        if ($conflict_count > 0) {
            // Someone already booked this! Redirect with an error message.
            header("Location: booking_form.php?id=$room_id&error=already_booked");
            exit();
        }

        // --- 2. PROCEED WITH INSERT IF NO CONFLICT ---
        $sql = "INSERT INTO reservations (customer_id, room_id, check_in, check_out, payment_method, status) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$customer_id, $room_id, $check_in, $check_out, $payment_method])) {
            $last_id = $pdo->lastInsertId(); 
            header("Location: success.php?res_id=" . $last_id);
            exit();
        }

    } catch (PDOException $e) {
        // Log the error and redirect
        header("Location: index.php?error=db_error");
        exit();
    }

} else {
    header("Location: customer_login.php");
    exit();
}
?>