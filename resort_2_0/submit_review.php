<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['customer_id'])) {
    $cid = $_SESSION['customer_id'];
    $rate = $_POST['rating'];
    $msg = trim($_POST['comment']);

    if (!empty($rate) && !empty($msg)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (customer_id, rating, comment) VALUES (?, ?, ?)");
            $stmt->execute([$cid, $rate, $msg]);
            header("Location: index.php?msg=Review posted!");
            exit();
        } catch (PDOException $e) {
            header("Location: index.php?err=Submission failed");
            exit();
        }
    }
}
header("Location: index.php");
exit();