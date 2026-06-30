<?php
session_start();
require 'db.php';

// 1. Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_rooms.php");
    exit();
}

$id = $_GET['id'];

// 2. Fetch current room data
$stmt = $pdo->prepare("SELECT * FROM accommodations WHERE room_id = ?");
$stmt->execute([$id]);
$room = $stmt->fetch();

if (!$room) {
    die("Room not found.");
}

// 3. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['room_type'];
    $price = $_POST['price_per_night'];
    $status = $_POST['status'];
    $desc = $_POST['description'];
    
    // Handle Image Upload (Optional)
    $image_path = $room['image_path']; // Keep old image by default
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
        $image_path = $image_name;
    }

    try {
        $sql = "UPDATE accommodations SET 
                room_type = ?, 
                price_per_night = ?, 
                status = ?, 
                description = ?, 
                image_path = ? 
                WHERE room_id = ?";
        $pdo->prepare($sql)->execute([$type, $price, $status, $desc, $image_path, $id]);
        header("Location: manage_rooms.php?msg=updated");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Accommodation | Nana's Place Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Sidebar and Layout matching manage_rooms.php */
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar { height: 100vh; background: #03045e; color: white; padding: 20px; position: fixed; width: 250px; transition: 0.3s; }
        .main-content { margin-left: 250px; padding: 40px; }
        
        .nav-link { color: rgba(255,255,255,0.7); border-radius: 8px; margin-bottom: 5px; transition: 0.3s; text-decoration: none; display: block; padding: 10px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.1); }
        .nav-link i { width: 25px; }

        /* Content Card matching manage_rooms.php */
        .content-card { background: white; border-radius: 15px; border: none; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; }
        .form-control, .form-select { border-radius: 10px; padding: 10px; border: 1px solid #dee2e6; }
        .btn-save { background: #0077b6; color: white; border-radius: 10px; font-weight: 600; padding: 10px 25px; border: none; transition: 0.3s; }
        .btn-save:hover { background: #03045e; color: white; transform: translateY(-2px); }
        .text-navy { color: #03045e; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="text-center mb-4">
        <img src="logo n.jpg" width="60" height="60" class="rounded-circle border mb-2">
        <h5 class="fw-bold">NANA'S ADMIN</h5>
        <span class="badge bg-info small"><?= $_SESSION['role'] ?? 'Staff' ?></span>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="admin_panel.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="manage_rooms.php"><i class="fas fa-bed me-2"></i> Accommodations</a></li>
        <li class="nav-item"><a class="nav-link" href="manage_reservations.php"><i class="fas fa-calendar-check me-2"></i> Reservations</a></li>
        <li class="nav-item"><a class="nav-link" href="customer_list.php"><i class="fas fa-users me-2"></i> Guests</a></li>
        <li class="nav-item mt-4"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<main class="main-content">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="manage_rooms.php" class="text-decoration-none">Accommodations</a></li>
                <li class="breadcrumb-item active">Edit Cottage</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-1">Edit Cottage Details</h2>
        <p class="text-muted">Update the information for cottage #<?= htmlspecialchars($id) ?></p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="content-card">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Cottage Name/Type</label>
                            <input type="text" name="room_type" class="form-control" value="<?= htmlspecialchars($room['room_type']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price per Night (PHP)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-peseta-sign text-muted"></i></span>
                                <input type="number" name="price_per_night" class="form-control border-start-0" value="<?= htmlspecialchars($room['price_per_night']) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Availability Status</label>
                        <select name="status" class="form-select">
                            <option value="Available" <?= $room['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Occupied" <?= $room['status'] == 'Occupied' ? 'selected' : '' ?>>Occupied/Booked</option>
                            <option value="Maintenance" <?= $room['status'] == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                        <small class="text-muted mt-1 d-block">Setting this to 'Available' will put it back on the homepage.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($room['description']) ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Accommodation Image</label>
                        <div class="d-flex align-items-center bg-light p-3 rounded-3 border">
                            <img src="uploads/<?= $room['image_path'] ?: 'logo n.jpg' ?>" width="100" class="rounded me-3 border" onerror="this.src='logo n.jpg'">
                            <div>
                                <input type="file" name="image" class="form-control mb-1">
                                <small class="text-muted">Leave blank to keep current image</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="manage_rooms.php" class="btn btn-light rounded-pill px-4">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button type="submit" class="btn btn-save rounded-pill px-5 shadow-sm">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="content-card mb-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-info"></i> Quick Tip</h5>
                <p class="small text-muted mb-0">Changes saved here will reflect immediately on the guest booking page. Ensure the price and status are accurate before saving.</p>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>