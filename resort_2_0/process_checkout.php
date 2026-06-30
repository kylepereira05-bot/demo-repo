<?php
require 'db.php';

if (isset($_GET['res_id'])) {
    $res_id = $_GET['res_id'];

    // 1. Mark reservation as Completed
    $stmt1 = $pdo->prepare("UPDATE reservations SET status = 'Completed' WHERE res_id = ?");
    $stmt1->execute([$res_id]);

    // 2. Make the room available on the homepage again
    $stmt2 = $pdo->prepare("UPDATE accommodations SET status = 'Available' 
                            WHERE room_id = (SELECT room_id FROM reservations WHERE res_id = ?)");
    $stmt2->execute([$res_id]);

    header("Location: manage_reservations.php?msg=checkedout");
}
?>