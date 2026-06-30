<?php
session_start();
require 'db.php';

// 1. Security Check: Is the user logged in?
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// 2. Data Check: Is there an ID in the URL?
if (isset($_GET['id'])) {
    $reservation_id = $_GET['id'];
    $customer_id = $_SESSION['customer_id'];

    try {
        // 3. Update Query: Only cancel if it belongs to this user and is still 'PENDING'
        // Inside cancel_booking.php
$query = "UPDATE reservations 
          SET status = 'CANCELLED' 
          WHERE res_id = ? AND customer_id = ? AND status = 'PENDING'";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$reservation_id, $customer_id]);

        // Check if a row was actually changed
        if ($stmt->rowCount() > 0) {
            $msg = "Reservation cancelled successfully.";
            header("Location: customer_dashboard.php?msg=" . urlencode($msg));
        } else {
            $error = "Unable to cancel. The reservation may already be processed or not found.";
            header("Location: customer_dashboard.php?error=" . urlencode($error));
        }
        exit();

    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        header("Location: customer_dashboard.php?error=" . urlencode($error));
        exit();
    }
} else {
    // If someone tries to access the file directly without an ID
    header("Location: customer_dashboard.php");
    exit();
}