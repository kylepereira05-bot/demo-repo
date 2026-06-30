<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = $_POST['username']; // This accepts Username or Phone
    $password = $_POST['password'];

    // 1. SEARCH IN CUSTOMERS TABLE
    // Matches screenshot: user_name, contact_number, password, customer_id, fullname
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE user_name = ? OR contact_number = ?");
    $stmt->execute([$login_input, $login_input]);
    $customer = $stmt->fetch();

    if ($customer && password_verify($password, $customer['password'])) {
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['fullname'] = $customer['fullname'];
        $_SESSION['role'] = 'customer';
        header("Location: index.php");
        exit();
    }

    // 2. SEARCH IN STAFF TABLE
    // Matches screenshot: user_name, password, staff_id, role, fullname
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE user_name = ?");
    $stmt->execute([$login_input]);
    $staff = $stmt->fetch();

    if ($staff && password_verify($password, $staff['password'])) {
    $_SESSION['staff_id'] = $staff['staff_id'];
    $_SESSION['role'] = $staff['role']; 
    $_SESSION['full_name'] = $staff['fullname'];
    $_SESSION['staff_name'] = $staff['fullname']; // Add this line!
    
    header("Location: admin_panel.php");
    exit();
}

    // 3. IF NO MATCH IN EITHER TABLE
    header("Location: login_page.php?error=invalid_credentials");
    exit();
}
?>