<?php
session_start();
require 'db.php';

// 1. Security: Ensure a reservation ID exists in the URL
if (!isset($_GET['res_id'])) {
    header("Location: index.php");
    exit();
}

$res_id = $_GET['res_id'];

// 2. Fetch the reservation details to show the guest
try {
    $stmt = $pdo->prepare("
        SELECT r.*, a.room_type, a.room_price, c.fullname 
        FROM reservations r 
        JOIN accommodations a ON r.room_id = a.room_id 
        JOIN customers c ON r.customer_id = c.customer_id 
        WHERE r.res_id = ?
    ");
    $stmt->execute([$res_id]);
    $booking = $stmt->fetch();

    if (!$booking) { die("Reservation not found."); }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(rgba(3, 4, 94, 0.8), rgba(3, 4, 94, 0.8)), url('Backpic.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
            display: flex;
            align-items: center;
            min-height: 100vh;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 40px;
            max-width: 600px;
            margin: auto;
            text-align: center;
        }
        .receipt-line {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 0;
            font-size: 0.9rem;
        }
        .check-icon {
            font-size: 4rem;
            color: #58a0d3;
            margin-bottom: 20px;
        }
        .btn-print { background: #58a0d3; color: white; border: none; font-weight: 600; }
        .btn-print:hover { background: #4682b4; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="success-card shadow-lg">
        <i class="fas fa-check-circle check-icon"></i>
        <h2 class="fw-bold">Booking Confirmed!</h2>
        <p class="text-white-50">Thank you for choosing Nana's Place, <?= htmlspecialchars($booking['fullname']) ?>.</p>
        
        <div class="my-4 p-3 bg-white bg-opacity-10 rounded-3 text-start">
            <h6 class="text-info fw-bold text-uppercase small mb-3">Reservation Details</h6>
            
            <div class="receipt-line">
                <span>Reference ID:</span>
                <span class="fw-bold text-info">#RE-<?= str_pad($booking['res_id'], 5, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="receipt-line">
                <span>Accommodation:</span>
                <span><?= htmlspecialchars($booking['room_type']) ?></span>
            </div>
            <div class="receipt-line">
                <span>Check-in:</span>
                <span><?= date('M d, Y', strtotime($booking['check_in'])) ?></span>
            </div>
            <div class="receipt-line">
                <span>Check-out:</span>
                <span><?= date('M d, Y', strtotime($booking['check_out'])) ?></span>
            </div>
            <div class="receipt-line border-0">
                <span class="fw-bold">Status:</span>
                <span class="badge bg-warning text-dark"><?= $booking['status'] ?></span>
            </div>
        </div>

        <p class="small text-white-50 mb-4">Please present this reference ID upon arrival at the resort.</p>

        <div class="d-grid gap-2">
            <button onclick="window.print()" class="btn btn-print py-2">
                <i class="fas fa-print me-2"></i> Print Receipt
            </button>
            <a href="index.php" class="btn btn-outline-light py-2">Return to Home</a>
        </div>
    </div>
</div>

</body>
</html>
