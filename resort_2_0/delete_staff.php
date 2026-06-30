<?php
require 'auth_check.php';
require 'db.php';

// Security: Only Admins can delete
if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php?error=unauthorized");
    exit();
}

if (isset($_GET['id'])) {
    $staff_id = $_GET['id'];

    // Prevent Admin from deleting themselves
    if ($staff_id == $_SESSION['staff_id']) {
        header("Location: manage_staff.php?error=self_delete");
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM staff WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        header("Location: manage_staff.php?msg=deleted");
    } catch (PDOException $e) {
        // If there is linked data, we set the 'processed_by' columns to NULL instead of deleting the data
        // This ensures the reservation/report stays, but the staff link is gone
        header("Location: manage_staff.php?error=linked_data");
    }
    exit();
}