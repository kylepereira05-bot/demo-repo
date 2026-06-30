<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SECURE: Hash the new password provided by the guest
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    $sql = "UPDATE customers SET password = ? WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$new_password, $customer_id])) {
        header("Location: customer_login.php?msg=pw_updated");
        exit();
    } else {
        $error = "Error updating password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | Nana's Place</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h4 class="text-center mb-4">Set New Password</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-success w-100">Update Password</button>
        </form>
    </div>
</body>
</html>