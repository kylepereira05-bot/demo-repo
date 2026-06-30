<?php 
session_start(); 
// Note: Ensure your db.php is required if not already handled in login_process.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Nana's Place Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Exact Background and Layout from customer_login.php */
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
        .btn-navy { 
            background-color: #03045e; 
            color: white; 
            border: none; 
            padding: 12px; 
            font-weight: 600; 
            border-radius: 10px; 
            transition: 0.3s; 
        }
        .btn-navy:hover { 
            background-color: #023e8a; 
            color: white; 
            transform: translateY(-2px); 
        }
        .form-control { border-radius: 10px; padding: 12px; }
        .input-group-text { background: #f8f9fa; border-right: none; }
        .form-control { border-left: none; }
        .text-primary-nana { color: #03045e; }
    </style>
</head>
<body>

<div class="login-card text-center">
    <div class="mb-4">
        <img src="logo n.jpg" width="70" height="70" class="rounded-circle mb-3 border shadow-sm" onerror="this.src='https://via.placeholder.com/70'">
        <h3 class="fw-bold text-dark">Nana's Portal</h3>
        <p class="text-muted small">Guest Access Portal</p>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger py-2 small border-0 mb-4 text-start">
            <i class="fas fa-exclamation-circle me-2"></i> Invalid credentials.
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
        <div class="alert alert-success py-2 small border-0 mb-4 text-start">
            <i class="fas fa-check-circle me-2"></i> Registration successful! Please log in.
        </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
        <div class="mb-3 text-start">
            <label class="form-label small fw-bold text-secondary text-uppercase">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>
        </div>

        <div class="mb-2 text-start">
            <label class="form-label small fw-bold text-secondary text-uppercase">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" id="password_field" class="form-control" placeholder="Enter password" required>
            </div>
        </div>

        <div class="text-end mb-4">
            <a href="forgot_password.php" class="small text-decoration-none text-muted">Forgot Password?</a>
        </div>

        <button type="submit" class="btn btn-navy w-100 mb-3 shadow-sm">Sign In to Account</button>
    </form>

    <div class="border-top pt-3 mt-2">
        <p class="small text-muted mb-1">New guest? <a href="register.php" class="text-primary-nana fw-bold text-decoration-none">Create Account</a></p>
        <a href="index.php" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1"></i> Back to Home</a>
    </div>
</div>  

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>