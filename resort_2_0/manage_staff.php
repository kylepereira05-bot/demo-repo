<?php
require 'auth_check.php';
require 'db.php';        

// Role-Based Security: Only Admins can manage staff
if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

// Handle Adding New Staff
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_staff'])) {
    $fullname = $_POST['fullname'];
    $user_name = $_POST['user_name'];
    
    // SECURE: Hash the password using PHP's built-in hashing algorithm
    // This replaces the previous plain-text storage method.
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    $role = $_POST['role'];

    // SQL matches ERD: Sets status to 'Active' and is_active to 1
    $sql = "INSERT INTO staff (fullname, user_name, password, role, status, is_active) 
            VALUES (?, ?, ?, ?, 'Active', 1)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$fullname, $user_name, $password, $role])) {
        header("Location: manage_staff.php?msg=added");
        exit();
    } else {
        $error_msg = "Error adding staff member.";
    }
}

// Fetch all staff members to display in the table
$staff_list = $pdo->query("SELECT * FROM staff ORDER BY role ASC")->fetchAll();
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
    <h2>Staff Management</h2>
    <hr>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success">New staff member registered successfully!</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Add New Staff</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="user_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select">
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" name="add_staff" class="btn btn-primary w-100">Register Staff</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff_list as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['fullname']) ?></td>
                                <td><?= htmlspecialchars($s['user_name']) ?></td>
                                <td>
                                    <span class="badge <?= $s['role'] == 'Admin' ? 'bg-danger' : 'bg-info' ?>">
                                        <?= $s['role'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $s['status'] == 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $s['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="reset_password.php?id=<?= $s['staff_id'] ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    
                                    <?php if ($s['staff_id'] != $_SESSION['staff_id']): ?>
                                        <a href="delete_staff.php?id=<?= $s['staff_id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Delete this account?')">
                                           <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>