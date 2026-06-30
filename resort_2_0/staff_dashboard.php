<?php
session_start();
// Security Check: Only allow 'staff' or 'admin'
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header("Location: login_page.php");
    exit();
}
require 'db.php';

// Fetch all PENDING bookings for the staff to process
$stmt = $pdo->query("SELECT b.*, c.fullname, a.room_type 
                     FROM bookings b 
                     JOIN customers c ON b.customer_id = c.customer_id 
                     JOIN accommodations a ON b.room_id = a.room_id 
                     WHERE b.status = 'Pending' 
                     ORDER BY b.booking_date DESC");
$pending_bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Portal | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { background: #023e8a; min-height: 100vh; color: white; }
        .nav-link { color: rgba(255,255,255,0.8); transition: 0.3s; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.1); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-4">
            <h4 class="fw-bold mb-4">Staff Desk</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="#" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="manage_bookings.php" class="nav-link"><i class="fas fa-calendar-check me-2"></i> Bookings</a></li>
                <li class="nav-item mb-2"><a href="index.php" class="nav-link"><i class="fas fa-eye me-2"></i> View Site</a></li>
                <hr>
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            </ul>
        </div>

        <div class="col-md-10 p-5 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h2>
                <span class="badge bg-info text-dark px-3 py-2">Role: Staff Member</span>
            </div>

            <div class="card p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-clock text-warning me-2"></i> Pending Confirmations</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Guest</th>
                                <th>Room Type</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pending_bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['fullname']) ?></td>
                                <td><?= htmlspecialchars($booking['room_type']) ?></td>
                                <td><?= $booking['check_in'] ?></td>
                                <td><?= $booking['check_out'] ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm px-3 rounded-pill">Approve</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(!$pending_bookings): ?>
                                <tr><td colspan="5" class="text-center text-muted">No pending bookings today.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>