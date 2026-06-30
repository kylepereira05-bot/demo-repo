<?php
session_start();
require 'db.php';

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php?msg=Please login to book a room");
    exit();
}

// 2. Data Fetching: Get room info from URL ID
$room_id = $_GET['id'] ?? null;
if (!$room_id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM accommodations WHERE room_id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) { 
    die("Room not found."); 
}

// 3. Date Configuration
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?= htmlspecialchars($room['room_type']) ?> | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .booking-card { border-radius: 15px; border: none; overflow: hidden; }
        .header-box { background: #03045e; color: white; padding: 25px; }
        
        /* Interactive Payment Radio Styling */
        .payment-option input { display: none; }
        .payment-box {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .payment-option input:checked + .payment-box {
            border-color: #00b4d8;
            background-color: #f0faff;
            color: #03045e;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .btn-navy { background: #03045e; color: white; border-radius: 10px; font-weight: 600; transition: 0.3s; }
        .btn-navy:hover { background: #023e8a; color: white; transform: translateY(-1px); }
        
        .price-summary { background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 12px; }
        #qr_section { transition: all 0.4s ease-in-out; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5"> 
            
            <?php if (isset($_GET['error']) && $_GET['error'] == 'already_booked'): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center">
                    <i class="fas fa-calendar-times me-3 fs-4"></i>
                    <div>
                        <strong>Date Conflict!</strong> Someone just booked these dates. Please choose a different schedule.
                    </div>
                </div>
            <?php endif; ?>

            <div class="card booking-card shadow-lg">
                <div class="header-box text-center">
                    <h4 class="fw-bold mb-1">Confirm Reservation</h4>
                    <p class="small mb-0 opacity-75"><?= htmlspecialchars($room['room_type']) ?> - Nana's Place</p>
                </div>
                
                <div class="card-body p-4">
                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Check-in</label>
                                <input type="date" name="check_in" id="check_in" class="form-control" required min="<?= $today ?>" onchange="calculateTotal()">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Check-out</label>
                                <input type="date" name="check_out" id="check_out" class="form-control" required min="<?= $tomorrow ?>" onchange="calculateTotal()">
                            </div>

                            <div class="col-12 mt-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Payment Method</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="payment-option w-100">
                                            <input type="radio" name="payment_method" value="Walk-in" checked onclick="toggleQR(false)">
                                            <div class="payment-box">
                                                <i class="fas fa-walking mb-1 d-block"></i>
                                                <span class="small fw-bold">Walk-in</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="payment-option w-100">
                                            <input type="radio" name="payment_method" value="Online" onclick="toggleQR(true)">
                                            <div class="payment-box">
                                                <i class="fas fa-qrcode mb-1 d-block"></i>
                                                <span class="small fw-bold">Online      </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="qr_section" class="col-12 mt-3" style="display: none;">
                                <div class="card border-info bg-light-subtle">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold mb-2 text-primary small">Scan QR to Pay</h6>
                                        <img src="Nana's code.jpg" alt="GCash QR" class="img-fluid rounded border mb-2 shadow-sm" style="max-width: 140px;" onerror="this.src='https://via.placeholder.com/150?text=GCash+QR'">
                                        <p class="mb-0 fw-bold small text-dark">Nana's Place Resort</p>
                                        <p class="mb-0 text-muted" style="font-size: 0.7rem;">09XX-XXX-XXXX</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="price-summary p-3 text-center">
                                    <span class="text-muted small d-block">Total Computation:</span>
                                    <span class="h3 fw-bold mb-0 text-dark" id="display_total">₱<?= number_format($room['price_per_night'], 2) ?></span>
                                    <small class="text-muted d-block mt-1" id="night_count">1 Night stay</small>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-navy w-100 py-3 shadow-sm border-0">
                                    <i class="fas fa-check-circle me-2"></i> Book Reservation
                                </button>
                                <div class="text-center mt-3">
                                    <a href="index.php" class="text-muted small text-decoration-none">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Logic to show/hide the GCash QR code
    function toggleQR(show) {
        const qrSection = document.getElementById('qr_section');
        qrSection.style.display = show ? 'block' : 'none';
    }

    // Logic to calculate total price based on dates
    function calculateTotal() {
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        const ratePerNight = <?= (float)$room['price_per_night'] ?>;
        
        if (checkIn && checkOut) {
            const start = new Date(checkIn);
            const end = new Date(checkOut);
            
            if (end > start) {
                // Calculate difference in days
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                
                const total = diffDays * ratePerNight;
                
                // Update the Display
                document.getElementById('display_total').innerText = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('night_count').innerText = diffDays + (diffDays > 1 ? ' Nights stay' : ' Night stay');
            } else {
                document.getElementById('display_total').innerText = '₱0.00';
                document.getElementById('night_count').innerText = 'Invalid Check-out Date';
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>