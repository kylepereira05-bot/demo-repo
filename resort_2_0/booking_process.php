<?php
session_start();
require 'db.php';

// 1. Security Check: Ensure the user is logged in as a Customer
if (!isset($_SESSION['customer_id'])) {
    // If not logged in, save their intended booking in a session and send to login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: customer_login.php?msg=login_required");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 2. Collect and Clean Inputs
    $customer_id = $_SESSION['customer_id'];
    $room_id     = $_POST['room_id'];
    $check_in    = $_POST['check_in'];
    $check_out   = $_POST['check_out'];
    $status      = 'Pending'; // Default status from your ERD

    try {
    // ... reservation INSERT code ...

    // UPDATED: Using 'status' to match your database column for 'Available/Occupied'
    $update_room = $pdo->prepare("UPDATE accommodations SET status = 'Occupied' WHERE room_id = ?");
    $update_room->execute([$room_id]);

    header("Location: booking_success.php?res_id=" . $pdo->lastInsertId());
    exit();
} catch (PDOException $e) {
    die("Booking Error: " . $e->getMessage());
}
} else {
    header("Location: index.php");
    exit();
}
?>