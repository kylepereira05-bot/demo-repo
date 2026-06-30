<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $user_name = $_POST['user_name'];
    $contact_number = $_POST['contact_number']; 
    $password = $_POST['password'];

    $check = $pdo->prepare("SELECT customer_id FROM customers WHERE user_name = ? OR contact_number = ?");
    $check->execute([$user_name, $contact_number]);

    if ($check->rowCount() > 0) {
        $error = "Username or Contact Number is already registered.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
        $sql = "INSERT INTO customers (fullname, user_name, contact_number, password) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$fullname, $user_name, $contact_number, $hashed_password])) {
            header("Location: login_page.php?msg=registered");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Nana's Place</title>
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
            max-width: 500px; 
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
        .form-control { border-radius: 10px; padding: 10px; }
        .text-primary-nana { color: #03045e; }
    </style>
</head>
<body>

<div class="login-card text-center">
    <img src="logo n.jpg" width="70" height="70" class="rounded-circle mb-3 border shadow-sm" onerror="this.src='https://via.placeholder.com/70'">
    <h3 class="fw-bold text-dark">Nana's Portal</h3>
    <p class="text-muted small mb-4">Create your guest account</p>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2 small border-0 mb-4 text-start">
            <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label class="form-label text-uppercase">Full Name</label>
            <input type="text" name="fullname" class="form-control" required>
        </div>

        <div class="row mb-3 text-start">
            <div class="col">
                <label class="form-label text-uppercase">Username</label>
                <input type="text" name="user_name" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label text-uppercase">Contact</label>
                <input type="text" name="contact_number" class="form-control" pattern="09[0-9]{9}" placeholder="09*********" required>
            </div>
        </div>

        <div class="mb-4 text-start">
            <label class="form-label text-uppercase">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-navy w-100 mb-3 shadow-sm">Complete Registration</button>
    </form>

    <div class="border-top pt-3 mt-2">
        <p class="small text-muted mb-0">Already a member? <a href="login_page.php" class="text-primary-nana fw-bold text-decoration-none">Sign In</a></p>
    </div>
</div>  

</body>
</html>