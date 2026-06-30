<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

try {
    // Fetch bookings joined with accommodation details
    // Ensure these column names match your actual database (e.g., reservation_id vs res_id)
    $query = "SELECT r.*, a.room_type, a.price_per_night, a.image_path 
              FROM reservations r 
              JOIN accommodations a ON r.room_id = a.room_id 
              WHERE r.customer_id = ? 
              ORDER BY r.check_in DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$customer_id]);
    $my_bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --nana-navy: #03045e;
            --nana-light-blue: #58a0d3;
        }

        body {
            background-color: #f4f7f6;
            font-family: 'Poppins', sans-serif;
        }

        .nav-nana {
            background: var(--nana-navy);
            padding: 12px 0;
        }

        .btn-home {
            background: white;
            color: var(--nana-navy);
            border: none;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-home:hover {
            background: var(--nana-light-blue);
            color: white;
        }

        .dashboard-header {
            background: white;
            padding: 40px 0;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .booking-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            margin-bottom: 25px;
            background: white;
        }

        .booking-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .status-badge {
            border-radius: 50px;
            padding: 6px 18px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .room-thumb {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 15px;
            border: 2px solid #eee;
        }

        .text-navy {
            color: var(--nana-navy);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark nav-nana shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="logo n.jpg" width="40" height="40" class="rounded-circle me-2 border border-info">
                NANA'S PLACE
            </a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-home btn-sm px-4 rounded-pill me-3">
                    <i class="fas fa-home me-1"></i> Home
                </a>

                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i> Account
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item py-2" href="index.php#cottages"><i class="fas fa-plus-circle me-2 text-primary"></i>New Booking</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="dashboard-header text-center">
        <div class="container">
            <h2 class="text-muted mb-0">Manage your resort reservations and check-in details below.</h2>
        </div>
    </div>

    <div class="container pb-5">
        <div class="col-md-9 mx-auto">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($my_bookings)): ?>
            <div class="col-md-9 mx-auto text-center py-5 bg-white rounded-4 border shadow-sm">
                <i class="fas fa-calendar-times fa-4x text-light mb-3"></i>
                <h4 class="text-muted">You haven't made any bookings yet.</h4>
                <p class="text-muted small">Ready for a vacation? Browse our cottages now!</p>
                <a href="index.php#cottages" class="btn btn-primary px-5 rounded-pill mt-2">Explore Accommodations</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($my_bookings as $booking): ?>
                    <div class="col-md-9 mx-auto">
                        <div class="card booking-card shadow-sm border-start border-4 border-info">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center text-md-start">
                                        <?php
                                        $imgPath = 'uploads/' . ($booking['image_path'] ?: 'logo n.jpg');
                                        if (!file_exists($imgPath)) {
                                            $imgPath = 'logo n.jpg';
                                        }
                                        ?>
                                        <img src="<?= $imgPath ?>" class="room-thumb mb-3 mb-md-0 shadow-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="fw-bold text-navy mb-1"><?= htmlspecialchars($booking['room_type']) ?></h5>
                                        <div class="text-muted small">
                                            <i class="far fa-calendar-check me-2 text-info"></i>
                                            <strong>Check-in:</strong> <?= date('M d, Y', strtotime($booking['check_in'])) ?>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="far fa-calendar-times me-2 text-danger"></i>
                                            <strong>Check-out:</strong> <?= date('M d, Y', strtotime($booking['check_out'])) ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <div class="h5 fw-bold text-navy mb-2">₱<?= number_format($booking['price_per_night'], 2) ?></div>

                                        <?php
                                        $status = strtoupper($booking['status'] ?? 'PENDING');
                                        switch ($status) {
                                            case 'APPROVED':
                                            case 'CONFIRMED':
                                                $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                                $icon = 'fa-check-circle';
                                                break;
                                            case 'REJECTED':
                                            case 'CANCELLED':
                                                $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                                                $icon = 'fa-times-circle';
                                                break;
                                            default:
                                                $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                                                $icon = 'fa-clock';
                                                break;
                                        }
                                        ?>

                                        <span class="status-badge <?= $badgeClass ?> d-inline-block mb-3 border">
                                            <i class="fas <?= $icon ?> me-1"></i> <?= $status ?>
                                        </span>

                                        <div class="action-buttons">
                                            <?php if ($status !== 'REJECTED' && $status !== 'CANCELLED'): ?>
                                                <a href="print_receipt.php?id=<?php echo $booking['res_id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill me-1 shadow-sm">
                                                    <i class="fas fa-print me-1"></i> Receipt
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($status === 'PENDING'): ?>
                                                <a href="cancel_booking.php?id=<?php echo $booking['res_id']; ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-pill shadow-sm"
                                                    onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                                    <i class="fas fa-times me-1"></i> Cancel
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>