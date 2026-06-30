<?php
session_start();
require 'db.php';

// Redirect to login if the guest is not signed in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? 'Guest';

// Handle Success/Error Messages from URL
$message = $_GET['msg'] ?? null;
$error = $_GET['error'] ?? null;

try {
    // JOIN reservations with accommodations to get the Room Type and Image
    $query = "SELECT r.*, a.room_type, a.image_path 
              FROM reservations r 
              JOIN accommodations a ON r.room_id = a.room_id 
              WHERE r.customer_id = ? 
              ORDER BY r.booking_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$customer_id]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading your bookings. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: #03045e !important;
        }

        .booking-card {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
            background: white;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Dynamic Status Colors */
        .bg-pending {
            background: #fff3cd;
            color: #856404;
        }

        .bg-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .bg-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .bg-completed {
            background: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">NANA'S PLACE</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Back to Home</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <h2 class="fw-bold text-dark">My Reservations</h2>
            <span class="text-muted small">Welcome, <strong><?= htmlspecialchars($customer_name) ?></strong></span>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-12">
                        <div class="booking-card d-md-flex p-3 align-items-center">
                            <img src="uploads/<?= $booking['image_path'] ?: 'logo n.jpg' ?>"
                                style="width: 150px; height: 110px; object-fit: cover; border-radius: 12px;"
                                onerror="this.src='logo n.jpg'">

                            <div class="ms-md-4 mt-3 mt-md-0 flex-grow-1">
                                <div class="row align-items-center">
                                    <div class="col-lg-5">
                                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($booking['room_type']) ?></h5>
                                        <p class="text-muted small mb-3 mb-lg-0">Booking Date: <?= date('M d, Y', strtotime($booking['booking_date'])) ?></p>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <small class="text-muted d-block">Stay Period</small>
                                        <span class="fw-bold small"><?= date('M d', strtotime($booking['check_in'])) ?> - <?= date('M d, Y', strtotime($booking['check_out'])) ?></span>
                                    </div>
                                    <div class="col-6 col-lg-2">
                                        <small class="text-muted d-block">Total Paid</small>
                                        <span class="fw-bold text-primary">₱<?= number_format($booking['total_price'], 2) ?></span>
                                    </div>

                                    <div class="col-lg-2 text-lg-end mt-3 mt-lg-0">
                                        <div class="d-flex flex-column align-items-lg-end align-items-start">
                                            <span class="status-badge bg-<?= strtolower($booking['status']) ?> mb-2">
                                                <?= ucfirst($booking['status']) ?>
                                            </span>

                                            <?php if (strtolower($booking['status']) == 'pending'): ?>
                                                <a href="cancel_booking.php?id=<?= $booking['reservation_id'] ?>"
                                                    class="text-danger small fw-bold text-decoration-none"
                                                    onclick="return confirm('Are you sure you want to cancel this reservation? The cottage will be released for others to book.')">
                                                    <i class="fas fa-times-circle me-1"></i> Cancel Booking
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded-4 shadow-sm border">
                        <i class="fas fa-calendar-alt fa-4x text-light mb-3"></i>
                        <h4 class="text-muted">No reservations found.</h4>
                        <p class="text-muted mb-4">Start your journey with us by booking a cottage today!</p>
                        <a href="index.php#rooms" class="btn btn-primary rounded-pill px-4">Browse Cottages</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>