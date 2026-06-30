<?php
session_start();
require 'db.php';

if (!isset($_GET['res_id'])) {
    header("Location: manage_reservations.php");
    exit();
}

$res_id = $_GET['res_id'];

// Fetch booking details to show the amount
$stmt = $pdo->prepare("SELECT r.*, a.price_per_night FROM reservations r 
                       JOIN accommodations a ON r.room_id = a.room_id WHERE r.res_id = ?");
$stmt->execute([$res_id]);
$booking = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Process Payment | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .payment-card { max-width: 500px; margin: 50px auto; border: none; border-radius: 15px; }
        .method-box { border: 2px solid #eee; border-radius: 10px; padding: 15px; cursor: pointer; transition: 0.3s; }
        .method-box:hover { border-color: #03045e; background: #f8f9ff; }
        input[type="radio"]:checked + .method-box { border-color: #03045e; background: #eef2ff; }
    </style>
</head>
<body>

<div class="container">
    <div class="card payment-card shadow-sm p-4">
        <h4 class="fw-bold text-center mb-4">Finalize Payment</h4>
        
        <div class="alert alert-info py-2">
            <strong>Total Amount:</strong> ₱<?= number_format($booking['price_per_night'], 2) ?>
        </div>

        <form action="save_payment.php" method="POST">
            <input type="hidden" name="res_id" value="<?= $res_id ?>">
            <input type="hidden" name="amount" value="<?= $booking['price_per_night'] ?>">

            <label class="form-label fw-bold mb-3">Select Payment Method:</label>
            
            <div class="mb-3">
                <input type="radio" name="payment_method" value="Walk-in" id="walkin" class="btn-check" checked>
                <label class="method-box d-block" for="walkin">
                    <i class="fas fa-walking me-2 text-primary"></i> 
                    <strong>Walk-In</strong>
                    <div class="small text-muted">Pay at the front desk upon arrival.</div>
                </label>
            </div>

            <div class="mb-4">
                <input type="radio" name="payment_method" value="Online" id="online" class="btn-check">
                <label class="method-box d-block" for="online">
                    <i class="fas fa-credit-card me-2 text-success"></i> 
                    <strong>Online Payment</strong>
                    <div class="small text-muted">GCash, Maya, or Credit Card.</div>
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                Confirm Payment
            </button>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>