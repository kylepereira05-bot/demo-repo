<?php
session_start();
require 'auth_check.php'; // Ensures the user is logged in
require 'db.php';         // Database connection

// Role-Based Security: Kick out if not Staff or Admin
if (!isset($_SESSION['role'])) {
    header("Location: login_page.php");
    exit();
}

// Fetch some quick stats for the dashboard
$total_rooms = $pdo->query("SELECT COUNT(*) FROM accommodations")->fetchColumn();
$pending_res = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'Pending'")->fetchColumn();
$total_staff = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; transition: 0.3s; }
        .main-content { margin-left: 250px; padding: 40px; }
        .stat-card { border: none; border-radius: 15px; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .nav-link { color: rgba(255,255,255,0.7); border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
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

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= htmlspecialchars($_SESSION['full_name']) ?>!</h2>
        <div class="text-muted"><?= date('F d, Y') ?></div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card stat-card bg-primary text-white p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total Rooms</h6>
                        <h2 class="fw-bold"><?= $total_rooms ?></h2>
                    </div>
                    <i class="fas fa-door-open fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-warning text-dark p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Pending Bookings</h6>
                        <h2 class="fw-bold"><?= $pending_res ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-success text-white p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total Staff</h6>
                        <h2 class="fw-bold"><?= $total_staff ?></h2>
                    </div>
                    <i class="fas fa-user-tie fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-4">Quick Actions</h4>
    <div class="row g-4">
        <?php if ($_SESSION['role'] === 'Admin'): ?>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-4">
                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                <h5>Add New Staff</h5>
                <p class="text-muted small">Register a new employee into the system.</p>
                <a href="manage_staff.php" class="btn btn-outline-primary">Open Staff Manager</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-4">
                <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                <h5>Add Accommodation</h5>
                <p class="text-muted small">Add new cottages or rooms for guests.</p>
                <a href="add_room.php" class="btn btn-outline-success">Add Room</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-4">
                <i class="fas fa-list-alt fa-3x text-info mb-3"></i>
                <h5>View Reservations</h5>
                <p class="text-muted small">Check all incoming guest bookings.</p>
                <a href="manage_reservations.php" class="btn btn-outline-info">Manage Bookings</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>