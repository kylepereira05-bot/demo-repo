<?php
session_start();
require 'db.php';

// 1. Fetch all registered customers
try {
    // Only selecting fields that exist in your database based on the ER Diagram
    $stmt = $pdo->query("SELECT customer_id, fullname, contact_number FROM customers ORDER BY customer_id DESC");
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching guests: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest List | Nana's Place Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; transition: 0.3s; }
        .main-content { margin-left: 250px; padding: 40px; }
        .nav-link { color: rgba(255, 255, 255, 0.7); border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255, 255, 255, 0.1); }
        .nav-link i { width: 25px; }
        .content-card { background: white; border-radius: 15px; border: none; padding: 25px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        .avatar-circle { width: 45px; height: 45px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #03045e; font-weight: bold; }
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
            <li class="nav-item"><a class="nav-link" href="admin_panel.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a></li>
            <?php if ($_SESSION['role'] === 'Admin'): ?>
                <li class="nav-item"><a class="nav-link" href="manage_staff.php"><i class="fas fa-users-cog me-2"></i> Manage Staff</a></li>
                <li class="nav-item"><a class="nav-link" href="revenue_report.php"><i class="fas fa-file-invoice-dollar me-2"></i> Revenue Report</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="manage_rooms.php"><i class="fas fa-bed me-2"></i>Accommodations</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_reservations.php"><i class="fas fa-calendar-check me-2"></i> Reservations</a></li>
            <a class="nav-link active" href="customer_list.php"><i class="fas fa-users"></i> Guests</a>
            <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <main class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Guest Registry</h2>
            <p class="text-muted">View all registered customers and their contact information.</p>
        </div>

        <div class="content-card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Guest</th>
                            <th>Contact Number</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No registered guests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($customers as $guest): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3">
                                                <?= strtoupper(substr($guest['fullname'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($guest['fullname']) ?></div>
                                                <small class="text-muted">ID: #CUST-<?= $guest['customer_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($guest['contact_number'] ?? 'Not provided') ?></td>
                                    <td class="text-end pe-3">
                                        <a href="view_customer_bookings.php?id=<?= $guest['customer_id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                            <i class="fas fa-history me-1"></i> View History
                                        </a>
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