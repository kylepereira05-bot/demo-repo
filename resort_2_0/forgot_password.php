<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contact_number = $_POST['contact_number'];
    
    try {
        $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE contact_number = ?");
        $stmt->execute([$contact_number]);
        $user = $stmt->fetch();

        if ($user) {
            header("Location: reset_guest_password.php?id=" . $user['customer_id']);
            exit();
        } else {
            $error = "No account found with that contact number.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('Backpic.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Poppins', sans-serif; 
        }
        .login-card { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 2.5rem; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.3); 
            width: 100%; 
            max-width: 400px; 
        }
        .btn-navy { 
            background: #03045e; 
            color: white; 
            border: none; 
            padding: 12px; 
            font-weight: 600; 
            border-radius: 10px; 
            transition: 0.3s; 
        }
        .btn-navy:hover { background: #023e8a; color: white; transform: translateY(-2px); }
        .form-label { font-size: 0.75rem; font-weight: 700; color: #6c757d; }
        .form-control { border-radius: 10px; padding: 12px; }
        .input-group-text { background: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
    </style>
</head>
<body>

<div class="login-card text-center">
    <img src="logo n.jpg" width="70" height="70" class="rounded-circle mb-3 border shadow-sm" onerror="this.src='https://via.placeholder.com/70'">
    <h3 class="fw-bold text-dark">Nana's Portal</h3>
    <p class="text-muted small mb-4">Account Recovery</p>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2 small border-0 mb-4 text-start">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4 text-start">
            <label class="form-label text-uppercase">Registered Contact Number</label>
            <div class="input-group shadow-sm">
                <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                <input type="text" name="contact_number" class="form-control" pattern="09[0-9]{9}" placeholder="09*********" required>
            </div>
            <div class="form-text mt-2" style="font-size: 0.65rem;">We'll use this to find your account details.</div>
        </div>

        <button type="submit" class="btn btn-navy w-100 mb-3 shadow-sm">Find My Account</button>
    </form>

    <div class="border-top pt-3 mt-2">
        <a href="login_page.php" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
    </div>
</div>  

</body>
</html>