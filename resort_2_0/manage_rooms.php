<?php
session_start();
require 'db.php';

// 1. Fetch all rooms/cottages
try {
    $stmt = $pdo->query("SELECT * FROM accommodations ORDER BY room_id DESC");
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms | Nana's Place Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; transition: 0.3s; }
        .main-content { margin-left: 250px; padding: 40px; }
        
        .nav-link { color: rgba(255,255,255,0.7); border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .nav-link i { width: 25px; }

        .content-card { background: white; border-radius: 15px; border: none; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .room-thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        .status-badge { border-radius: 50px; padding: 5px 12px; font-size: 0.75rem; font-weight: 600; }
        
        .btn-add { background: #0077b6; color: white; border-radius: 10px; font-weight: 600; }
        .btn-add:hover { background: #03045e; color: white; }
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Accommodation Management</h2>
            <p class="text-muted">Add, edit, or update your resort rooms and cottages.</p>
        </div>
        <a href="add_room.php" class="btn btn-add px-4 py-2 shadow-sm">
            <i class="fas fa-plus me-2"></i>Add New Cottage
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> Action successful: <strong><?= htmlspecialchars($_GET['msg']) ?></strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="content-card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Room Info</th>
                        <th>Type</th>
                        <th>Rate/Night</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <?php 
                                    $imgPath = 'uploads/' . ($room['image_path'] ?: 'logo n.jpg');
                                ?>
                                <img src="<?= $imgPath ?>" class="room-thumb me-3" onerror="this.src='logo n.jpg'">
                                <div class="small fw-bold">#<?= $room['room_id'] ?></div>
                            </div>
                        </td>
                        <td class="fw-semibold"><?= htmlspecialchars($room['room_type']) ?></td>
                        <td class="text-navy fw-bold">₱<?= number_format($room['price_per_night'], 2) ?></td>
                        <td>
                            <?php 
                                $status = $room['status'];
                                $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                if($status != 'Available') $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                            ?>
                            <span class="status-badge <?= $badgeClass ?>"><?= strtoupper($status) ?></span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="edit_room.php?id=<?= $room['room_id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="delete_room.php?id=<?= $room['room_id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this room?')">
                                <i class="fas fa-trash"></i>
                            </a>
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