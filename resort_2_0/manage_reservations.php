<?php
session_start();
require 'db.php';

// Fetch all reservations with related guest and cottage data
try {
    // Note: Ensure your 'reservations' table has the 'payment_method' column
    $query = "SELECT r.*, a.room_type, a.price_per_night, c.fullname 
              FROM reservations r 
              JOIN accommodations a ON r.room_id = a.room_id 
              JOIN customers c ON r.customer_id = c.customer_id 
              ORDER BY r.check_in DESC";
    $stmt = $pdo->query($query);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            height: 100vh;
            background: #03045e;
            color: white;
            padding: 20px;
            position: fixed;
            width: 250px;
            transition: 0.3s;
            z-index: 1000;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            margin-bottom: 5px;
            transition: 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            width: 25px;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            border: none;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .payment-badge {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 500;
        }

        .table thead th {
            background-color: #f8f9fa;
            border: none;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
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

            <li class="nav-item"><a class="nav-link" href="manage_rooms.php"><i class="fas fa-bed me-2"></i>Accommodations</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_reservations.php"><i class="fas fa-calendar-check me-2"></i> Reservations</a></li>
            <a class="nav-link" href="customer_list.php"><i class="fas fa-users"></i> Guests</a>
            <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <main class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold mb-1 text-dark">Reservation Management</h2>
            <p class="text-muted">Review, approve, or finalize guest bookings and check-outs.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead>
                        <tr>
                            <th class="ps-3">Guest Details</th>
                            <th>Cottage</th>
                            <th>Stay Period</th>
                            <th>Payment Method</th>
                            <th>Total Price</th>
                            <th>Booking Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($res['fullname']) ?></div>
                                    <small class="text-muted">Order #<?= $res['res_id'] ?></small>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= htmlspecialchars($res['room_type']) ?></span>
                                </td>
                                <td class="small">
                                    <div class="text-dark"><i class="far fa-calendar-alt me-1 text-primary"></i> <?= date('M d, Y', strtotime($res['check_in'])) ?></div>
                                    <div class="text-muted text-nowrap"><i class="fas fa-arrow-right me-1 small"></i> <?= date('M d, Y', strtotime($res['check_out'])) ?></div>
                                </td>

                                <td>
                                    <?php if ($res['payment_method'] == 'Online'): ?>
                                        <span class="payment-badge bg-primary-subtle text-primary border border-primary-subtle">
                                            <i class="fas fa-credit-card me-1"></i> Online
                                        </span>
                                    <?php else: ?>
                                        <span class="payment-badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                            <i class="fas fa-walking me-1"></i> Walk-in
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="fw-bold text-navy">₱<?= number_format($res['price_per_night'], 2) ?></td>
                                <td>
                                    <?php
                                    $status = strtoupper($res['status']);
                                    $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle'; // Pending
                                    if ($status == 'APPROVED' || $status == 'CONFIRMED') $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                    if ($status == 'COMPLETED' || $status == 'CHECKED-OUT') $badgeClass = 'bg-info-subtle text-info border-info-subtle';
                                    if ($status == 'REJECTED' || $status == 'CANCELLED') $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                                    ?>
                                    <span class="status-badge <?= $badgeClass ?> border"><?= $status ?></span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group shadow-sm rounded">
                                        <?php if ($status == 'PENDING'): ?>
                                            <a href="update_status.php?id=<?= $res['res_id'] ?>&action=approve" class="btn btn-sm btn-success px-3" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="update_status.php?id=<?= $res['res_id'] ?>&action=reject" class="btn btn-sm btn-danger px-3" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php elseif ($status == 'APPROVED' || $status == 'CONFIRMED'): ?>
                                            <a href="update_status.php?id=<?= $res['res_id'] ?>&action=checkout"
                                                class="btn btn-sm btn-primary px-3 fw-bold"
                                                onclick="return confirm('Complete this stay? The cottage will become Available on the homepage.')">
                                                <i class="fas fa-door-open me-1"></i> Check-out
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light disabled text-muted"><i class="fas fa-lock"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
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