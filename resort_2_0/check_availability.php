<?php
session_start();
require 'db.php'; // Database connection

// 1. Capture the room_id from the URL (e.g., ?room_id=5)
$selected_room_id = isset($_GET['room_id']) ? $_GET['room_id'] : '';

// 2. Fetch all rooms that are currently marked as 'Available'
try {
    // FIXED: Using 'status' to match your accommodations table screenshot
    $stmt = $pdo->query("SELECT * FROM accommodations WHERE status = 'Available'");
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Your Stay | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Immersive background matching your login and index pages */
        body { 
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('Backpic.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 550px;
            margin: auto;
        }
        .form-control, .form-select { background: white; border-radius: 8px; border: none; }
        .btn-book { background: #58a0d3; color: white; border: none; font-weight: bold; }
        .btn-book:hover { background: #4682b4; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="glass-card shadow">
        <div class="text-center mb-4">
            <img src="logo n.jpg" width="70" height="70" class="rounded-circle mb-3 border shadow-sm">
            <h3 class="fw-bold">Nana's Place</h3>
            <p class="small text-info">Confirm Dates & Cottage Selection</p>
        </div>

        <form action="booking_process.php" method="POST">
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label small fw-bold">Check-in</label>
                    <input type="date" name="check_in" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold">Check-out</label>
                    <input type="date" name="check_out" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label small fw-bold">Selected Cottage/Room</label>
                    <select name="room_id" class="form-select" required>
                        <option value="" disabled>Select a room...</option>
<?php foreach ($rooms as $room): ?>
    <option value="<?= $room['room_id'] ?>" <?= ($room['room_id'] == $selected_room_id) ? 'selected' : '' ?>>
        <?= htmlspecialchars($room['room_type']) ?> - ₱<?= number_format($room['price_per_night'], 2) ?>
    </option>
<?php endforeach; ?>    
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-book w-100 py-3 mt-2">
                PROCEED TO BOOKING <i class="fas fa-chevron-right ms-1"></i>
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="index.php" class="text-white-50 small text-decoration-none">Cancel and Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>