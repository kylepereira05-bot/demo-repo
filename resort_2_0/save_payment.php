<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $res_id = $_POST['res_id'];
    $method = $_POST['payment_method']; // 'Walk-in' or 'Online'
    $amount = $_POST['amount'];
    
    // In a real scenario, staff_id comes from the logged-in admin session
    $staff_id = $_SESSION['staff_id'] ?? 1; 

    try {
        $sql = "INSERT INTO payments (res_id, staff_id, payment_method, amount, payment_status) 
                VALUES (?, ?, ?, ?, 'Paid')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$res_id, $staff_id, $method, $amount]);

        // Optional: Update reservation status to show it's paid
        $pdo->prepare("UPDATE reservations SET status = 'Approved' WHERE res_id = ?")->execute([$res_id]);

        header("Location: manage_reservations.php?msg=Payment Recorded via " . $method);
    } catch (PDOException $e) {
        die("Payment Error: " . $e->getMessage());
    }
}