<?php
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// Get the Reservation ID from the URL
$res_id = $_GET['res_id'] ?? null;

if (!$res_id) {
    header("Location: customer_dashboard.php");
    exit();
}

try {
    // Fetch the booking details to show the payment method
    $query = "SELECT r.*, a.room_type 
              FROM reservations r 
              JOIN accommodations a ON r.room_id = a.room_id 
              WHERE r.res_id = ? AND r.customer_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$res_id, $_SESSION['customer_id']]);
    $booking = $stmt->fetch();

    if (!$booking) {
        die("Reservation not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Success | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .success-card { max-width: 500px; margin: 80px auto; border: none; border-radius: 20px; }
        .icon-circle { 
            width: 80px; height: 80px; background: #d1e7dd; color: #0f5132; 
            border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; font-size: 40px; margin: -40px auto 20px;
            border: 5px solid #f4f7f6;
        }
        .btn-navy { background: #03045e; color: white; border-radius: 10px; }
        .btn-navy:hover { background: #0077b6; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card success-card shadow-sm">
        <div class="card-body p-5 text-center">
            <div class="icon-circle">
                <i class="fas fa-check"></i>
            </div>
            
            <h2 class="fw-bold text-dark">Booking Successful!</h2>
            <p class="text-muted">Thank you for choosing Nana's Place. Your reservation has been received.</p>
            
            <div class="bg-light p-4 rounded-3 my-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Booking Reference:</span>
                    <span class="fw-bold">#<?= $booking['res_id'] ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Room Type:</span>
                    <span class="fw-bold"><?= htmlspecialchars($booking['room_type']) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Payment Method:</span>
                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                        <i class="fas <?= ($booking['payment_method'] == 'Online') ? 'fa-mobile-alt' : 'fa-walking' ?> me-1"></i>
                        <?= htmlspecialchars($booking['payment_method']) ?>
                    </span>
                </div>
            </div>

            <div class="d-grid gap-2">
    <a href="customer_dashboard.php" class="btn btn-navy py-3 fw-bold shadow-sm">
        <i class="fas fa-tasks me-2"></i> Go to My Dashboard
    </a>

    <a href="print_receipt.php?id=<?= $booking['res_id'] ?>" target="_blank" class="btn btn-outline-dark py-2 fw-bold">
        <i class="fas fa-print me-2"></i> Print My Receipt
    </a>

    <a href="index.php" class="btn btn-link text-muted text-decoration-none mt-2">
        <i class="fas fa-arrow-left me-1"></i> Back to Home
    </a>
</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>