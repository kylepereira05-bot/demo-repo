<?php
require 'auth_check.php';
require 'db.php';

if (isset($_GET['res_id'])) {
    $res_id = $_GET['res_id'];

    // SQL to join reservation, customer, and room details
    $query = "SELECT r.*, c.fullname, c.email, a.room_type, a.price_per_night 
              FROM reservations r
              JOIN customers c ON r.customer_id = c.customer_id
              JOIN accommodations a ON r.room_id = a.room_id
              WHERE r.res_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$res_id]);
    $data = $stmt->fetch();

    if (!$data) {
        die("Reservation not found.");
    }

    // Calculate total cost
    $check_in = new DateTime($data['check_in']);
    $check_out = new DateTime($data['check_out']);
    $diff = $check_in->diff($check_out);
$nights = $diff->days > 0 ? $diff->days : 1;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Receipt #<?= $res_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
        .receipt-box { border: 1px solid #ddd; padding: 30px; background: #fff; }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="receipt-box shadow-sm mx-auto" style="max-width: 600px;">
            <div class="text-center mb-4">
                <h2>Nana's Place Resort</h2>
                <p class="text-muted">Official Booking Receipt</p>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-6">
                    <strong>Receipt To:</strong><br>
                    <?= htmlspecialchars($data['fullname']) ?><br>
                    <?= htmlspecialchars($data['email']) ?>
                </div>
                <div class="col-6 text-end">
                    <strong>Reservation ID:</strong> #<?= $data['res_id'] ?><br>
                    <strong>Date:</strong> <?= date('M d, Y', strtotime($data['created_at'])) ?>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= htmlspecialchars($data['room_type']) ?><br>
                            <small class="text-muted"><?= $nights ?> Nights (<?= $data['check_in'] ?> to <?= $data['check_out'] ?>)</small>
                        </td>
                        <td class="text-end">$<?= number_format($total_price, 2) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
</body>
</html>