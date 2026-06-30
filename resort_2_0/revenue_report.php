<?php
session_start();
require 'db.php';

try {
    // 1. Fetch Total Revenue by Method
    $stmt = $pdo->query("SELECT payment_method, SUM(amount) as total 
                         FROM payments 
                         WHERE payment_status = 'Paid' 
                         GROUP BY payment_method");
    $method_totals = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $walkin_total = $method_totals['Walk-in'] ?? 0;
    $online_total = $method_totals['Online'] ?? 0;
    $grand_total = $walkin_total + $online_total;

    // 2. Fetch Detailed Transaction History
    // We JOIN with customers and accommodations to show WHO paid for WHAT
    $query = "SELECT p.*, c.fullname, a.room_type 
              FROM payments p
              JOIN reservations r ON p.res_id = r.res_id
              JOIN customers c ON r.customer_id = c.customer_id
              JOIN accommodations a ON r.room_id = a.room_id
              ORDER BY p.transaction_date DESC";
    $transactions = $pdo->query($query)->fetchAll();

} catch (PDOException $e) {
    die("Report Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Report | Nana's Place Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 40px; }
        
        .nav-link { color: rgba(255,255,255,0.7); border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }

        .stat-card { border: none; border-radius: 15px; background: white; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
        
        .table-card { background: white; border-radius: 15px; padding: 25px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .badge-method { border-radius: 50px; padding: 5px 12px; font-size: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4">
        <img src="logo n.jpg" width="60" height="60" class="rounded-circle border mb-2">
        <h5 class="fw-bold">NANA'S ADMIN</h5>
        <span class="badge bg-info small"><?= $_SESSION['role'] ?></span>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="admin_panel.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a></li>
        
        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <li class="nav-item"><a class="nav-link" href="manage_staff.php"><i class="fas fa-users-cog me-2"></i> Manage Staff</a></li>
            <li class="nav-item"><a class="nav-link" href="revenue_report.php"><i class="fas fa-file-invoice-dollar me-2"></i> Revenue Report</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="manage_rooms.php"><i class="fas fa-bed me-2"></i> Accommodations</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_reservations.php"><i class="fas fa-calendar-check me-2"></i> Reservations</a></li>
        <a class="nav-link" href="customer_list.php"><i class="fas fa-users"></i> Guests</a>
        <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<main class="main-content">
   <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Financial Reports</h2>
    <a href="export_revenue.php" class="btn btn-outline-success rounded-pill px-4">
        <i class="fas fa-file-excel me-2"></i>Generate Excel
    </a>
</div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-primary-subtle text-primary"><i class="fas fa-peseta-sign fa-lg"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold">Cash on hand Revenue</h6>
                <h3 class="fw-bold">₱<?= number_format($walkin_total, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-success-subtle text-success"><i class="fas fa-globe fa-lg"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold">Online Revenue</h6>
                <h3 class="fw-bold">₱<?= number_format($online_total, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-info">
                <div class="icon-box bg-info-subtle text-info"><i class="fas fa-wallet fa-lg"></i></div>
                <h6 class="text-muted small text-uppercase fw-bold">Grand Total</h6>
                <h3 class="fw-bold text-navy">₱<?= number_format($grand_total, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="table-card">
        <h5 class="fw-bold mb-4">Recent Transactions</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Guest</th>
                        <th>Cottage</th>
                        <th>Method</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td class="small"><?= date('M d, Y | h:i A', strtotime($tx['transaction_date'])) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($tx['fullname']) ?></td>
                        <td><?= htmlspecialchars($tx['room_type']) ?></td>
                        <td>
                            <?php 
                                $isWalkin = $tx['payment_method'] == 'Walk-in';
                                $class = $isWalkin ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success';
                            ?>
                            <span class="badge-method <?= $class ?> border">
                                <?= $tx['payment_method'] ?>
                            </span>
                        </td>
                        <td class="text-end fw-bold">₱<?= number_format($tx['amount'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>