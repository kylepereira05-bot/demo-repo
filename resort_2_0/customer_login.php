<?php
session_start();
require 'db.php'; // Database connection

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Fetch the guest by username
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_name = ?");
    $stmt->execute([$user_name]);
    $customer = $stmt->fetch();

    // 2. SECURE: Use password_verify to check the hashed password
    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['fullname'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nana's Place | Guest Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Matching the Staff Portal Background and Layout */
        body { 
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('Backpic.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            font-family: 'Poppins', sans-serif;
        }
        .login-card { 
            width: 100%; 
            max-width: 400px; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
            background: rgba(255, 255, 255, 0.95); 
        }
        /* Nana's Navy Theme */
        .btn-primary { background-color: #03045e; border: none; }
        .btn-primary:hover { background-color: #023e8a; }
        .text-primary-nana { color: #03045e; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <img src="logo n.jpg" width="70" height="70" class="rounded-circle mb-3 border shadow-sm">
        <h3 class="fw-bold text-dark">Nana's Place</h3>
        <p class="text-muted small">Guest Access Portal</p>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger p-2 small text-center border-0 mb-4">
            <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'pw_updated'): ?>
        <div class="alert alert-success p-2 small text-center border-0 mb-4">
            <i class="fas fa-check-circle me-1"></i> Password updated! Please login.
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary text-uppercase">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                <input type="text" name="user_name" class="form-control bg-light border-start-0" required placeholder="Enter username">
            </div>
        </div>
        
        <div class="mb-2">
            <label class="form-label small fw-bold text-secondary text-uppercase">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control bg-light border-start-0" required placeholder="Enter password">
            </div>
        </div>

        <div class="text-end mb-4">
            <a href="forgot_password.php" class="small text-decoration-none text-muted">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Login to Account</button>
    </form>
    
    <div class="text-center mt-4">
        <p class="small mb-1">New guest? <a href="register.php" class="text-decoration-none fw-bold text-primary-nana">Create an account</a></p>
        <a href="index.php" class="text-decoration-none small text-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Website
        </a>
    </div>
</div>

</body>
</html>