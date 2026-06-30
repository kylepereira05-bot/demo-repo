<?php
session_start();
require 'db.php';

// 1. Check if Customer ID is provided
if (!isset($_GET['id'])) {
    header("Location: customer_list.php");
    exit();
}

$customer_id = $_GET['id'];

try {
    // 2. Fetch Customer Details
    $stmtCust = $pdo->prepare("SELECT fullname, contact_number FROM customers WHERE customer_id = ?");
    $stmtCust->execute([$customer_id]);
    $customer = $stmtCust->fetch();

    if (!$customer) {
        die("Customer not found.");
    }

    // 3. Fetch all bookings for this specific customer
    // UPDATED: Using price_per_night to match your database
    $stmtBookings = $pdo->prepare("
        SELECT r.*, a.room_type, a.price_per_night 
        FROM reservations r 
        JOIN accommodations a ON r.room_id = a.room_id 
        WHERE r.customer_id = ? 
        ORDER BY r.check_in DESC
    ");
    $stmtBookings->execute([$customer_id]);
    $bookings = $stmtBookings->fetchAll();

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest History | Nana's Place Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 40px; }
        .nav-link { color: rgba(255,255,255,0.7); border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .content-card { background: white; border-radius: 15px; border: none; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .status-badge { border-radius: 50px; padding: 5px 12px; font-size: 0.7rem; font-weight: 600; }
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
        <div>
            <h2 class="fw-bold mb-1">Booking History</h2>
            <p class="text-muted">Showing all stays for <strong><?= htmlspecialchars($customer['fullname']) ?></strong></p>
        </div>
        <a href="customer_list.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <div class="content-card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Cottage/Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Price Paid</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="5" class="text-center py-4">No booking history for this guest.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $res): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($res['room_type']) ?></td>
                            <td><?= date('M d, Y', strtotime($res['check_in'])) ?></td>
                            <td><?= date('M d, Y', strtotime($res['check_out'])) ?></td>
                            <td class="text-primary fw-bold">₱<?= number_format($res['price_per_night'], 2) ?></td>
                            <td>
                                <?php 
                                    $status = strtoupper($res['status']);
                                    $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                                    if($status == 'APPROVED' || $status == 'COMPLETED') $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                    if($status == 'REJECTED') $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                                ?>
                                <span class="status-badge <?= $badgeClass ?> border"><?= $status ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>