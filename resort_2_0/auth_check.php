<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the staff_id is not in the session, they aren't logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: login_page.php");
    exit();
}
?>