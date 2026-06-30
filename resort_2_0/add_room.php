<?php
session_start();
require 'db.php';

// FIX: Allow both Admin and Staff to access this page
if (!isset($_SESSION['staff_id'])) {
    header("Location: login_page.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_room'])) {
    // FIX: Removed the Admin-only restriction here so Staff can also save
    $room_type = $_POST['room_type'];
    $price = $_POST['price_per_night'];
    $description = $_POST['description'];
    
    $image_path = "uploads/default.jpg"; 
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    try {
        $sql = "INSERT INTO accommodations (room_type, price_per_night, description, image_path, status) 
                VALUES (?, ?, ?, ?, 'Available')";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$room_type, $price, $description, $image_path])) {
            $message = "<div class='alert alert-success shadow-sm'><i class='fas fa-check-circle me-2'></i>Room successfully added!</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger shadow-sm'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Accommodation | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; z-index: 1000; }
        .main-content { margin-left: 250px; padding: 40px; }
        .nav-link { color: rgba(255, 255, 255, 0.7); border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255, 255, 255, 0.1); }
        .card { border: none; border-radius: 8px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .card-header { background-color: #007bff !important; padding: 15px 20px; }
        .card-header h4 { font-size: 1.15rem; font-weight: 500; }
        .form-control { border: 1px solid #ced4da !important; border-radius: 5px; padding: 8px 12px; }
        .form-control:focus { border-color: #007bff !important; box-shadow: none !important; outline: none; }
        .btn-primary { background-color: #007bff; border: none; padding: 10px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="text-center mb-4">
            <img src="logo n.jpg" width="60" height="60" class="rounded-circle border mb-2">
            <h5 class="fw-bold">NANA'S ADMIN</h5>
            <span class="badge bg-info small"><?php echo strtoupper($_SESSION['role']); ?></span>
        </div>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="admin_panel.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="add_room.php"><i class="fas fa-plus-circle me-2"></i> Add Room</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_rooms.php"><i class="fas fa-bed me-2"></i> Accommodations</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_reservations.php"><i class="fas fa-calendar-check me-2"></i> Reservations</a></li>
            <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <main class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6"> 
                    <div class="card shadow-sm mt-3">
                        <div class="card-header text-white">
                            <h4 class="mb-0">Add New Accommodation</h4>
                        </div>
                        <div class="card-body p-4">
                            <?= $message ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Room Type</label>
                                    <input type="text" name="room_type" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Price per Night (₱)</label>
                                    <input type="number" step="0.01" name="price_per_night" class="form-control" placeholder="0.00" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Enter amenities..." required></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold small">Room Image</label>
                                    <input type="file" name="image" class="form-control">
                                </div>
                                <button type="submit" name="add_room" class="btn btn-primary w-100 py-2">
                                    <i class="fas fa-save me-2"></i>Add Room to Inventory
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>