<?php
require 'auth_check.php'; // Ensures logged in
require 'db.php';         // Database connection

// Role-Based Security: Only Admins can access this page
if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

$message = "";

// Handle the Password Reset Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_id'])) {
    $staff_id = $_POST['reset_id'];
    $new_password = $_POST['new_password']; 

    // SECURE UPDATE: Hash the new password before saving
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the database with the hashed version
    $sql = "UPDATE staff SET password = ? WHERE staff_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$hashed_password, $staff_id])) {
        $message = "<div class='alert alert-success'>Password updated securely with encryption!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error updating password.</div>";
    }
}

// Fetch staff details to display who is being edited
$staff_to_edit = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE staff_id = ?");
    $stmt->execute([$_GET['id']]);
    $staff_to_edit = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Staff Password | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Reset Staff Password</h5>
                    </div>
                    <div class="card-body">
                        <?= $message ?>
                        
                        <?php if ($staff_to_edit): ?>
                            <p class="mb-4">Updating password for: <br>
                                <strong><?= htmlspecialchars($staff_to_edit['fullname']) ?></strong> 
                                (<?= htmlspecialchars($staff_to_edit['user_name']) ?>)
                            </p>
                            
                            <form method="POST" action="reset_password.php?id=<?= $staff_to_edit['staff_id'] ?>">
                                <input type="hidden" name="reset_id" value="<?= $staff_to_edit['staff_id'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">New Secure Password</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Enter at least 6 characters">
                                    <div class="form-text">System will automatically encrypt this password.</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Update Password</button>
                                <a href="manage_staff.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Back to Staff List</a>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">No staff member selected for password reset.</div>
                            <a href="manage_staff.php" class="btn btn-secondary w-100">Return to Staff List</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 