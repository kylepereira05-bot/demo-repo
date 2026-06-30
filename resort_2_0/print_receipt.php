<?php
session_start();
require 'db.php';

// 1. Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    die("Access Denied: Please log in first.");
}

// 2. Check if the ID is provided in the URL
if (!isset($_GET['id'])) {
    die("Access Denied: No reservation ID provided.");
}

$res_id = $_GET['id'];
$customer_id = $_SESSION['customer_id'];

try {
    // 3. Fetch reservation details AND ensure it belongs to the logged-in customer
    // This prevents User A from seeing User B's receipt by changing the URL ID
    $query = "SELECT r.*, a.room_type, a.price_per_night, c.fullname, c.contact_number
              FROM reservations r 
              JOIN accommodations a ON r.room_id = a.room_id 
              JOIN customers c ON r.customer_id = c.customer_id 
              WHERE r.res_id = ? AND r.customer_id = ?";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$res_id, $customer_id]);
    $receipt = $stmt->fetch();

    if (!$receipt) {
        die("Access Denied: Reservation not found or you do not have permission to view it.");
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $receipt['res_id']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background-color: #f0f0f0; padding: 20px; }
        .receipt-box { max-width: 500px; margin: auto; background: white; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 10px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { border-top: 2px solid #333; padding-top: 10px; font-weight: bold; font-size: 1.2em; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 0.8em; color: #666; }
        .btn-print { background: #03045e; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; margin-top: 20px; }
        @media print { .btn-print { display: none; } body { background: white; padding: 0; } .receipt-box { box-shadow: none; border: none; } }
    </style>
</head>
<body>

<div class="receipt-box">
    <div class="header">
        <h2 style="margin: 0;">NANA'S PLACE</h2>
        <p style="margin: 5px 0;">Official Receipt</p>
    </div>

    <div class="row"><span>Receipt ID:</span> <span>#<?php echo $receipt['res_id']; ?></span></div>
    <div class="row"><span>Date:</span> <span><?php echo date('M d, Y'); ?></span></div>
    <div class="row"><span>Customer:</span> <span><?php echo htmlspecialchars($receipt['fullname']); ?></span></div>
    
    <hr style="border: none; border-top: 1px solid #eee;">

    <div class="row"><span>Room Type:</span> <span><?php echo htmlspecialchars($receipt['room_type']); ?></span></div>
    <div class="row"><span>Check-in:</span> <span><?php echo $receipt['check_in']; ?></span></div>
    <div class="row"><span>Check-out:</span> <span><?php echo $receipt['check_out']; ?></span></div>
    <div class="row"><span>Status:</span> <span style="font-weight: bold;"><?php echo strtoupper($receipt['status']); ?></span></div>

    <div class="total">
        <div class="row"><span>Total Amount:</span> <span>₱<?php echo number_format($receipt['price_per_night'], 2); ?></span></div>
    </div>

    <div class="footer">
        <p>Thank you for choosing Nana's Place!<br>Please present this receipt upon arrival.</p>
        <button class="btn-print" onclick="window.print()">Click to Print</button>
    </div>
</div>

</body>
</html>