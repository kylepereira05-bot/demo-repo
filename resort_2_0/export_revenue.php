<?php
require 'db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Revenue_Report_'.date('Y-m-d').'.csv');

$output = fopen('php://output', 'w');

// Set Column Headers
fputcsv($output, array('Payment ID', 'Guest Name', 'Room Type', 'Amount', 'Date'));

// Fetch Data
$query = "SELECT p.payment_id, c.fullname, a.room_type, p.amount, p.transaction_date 
          FROM payments p
          JOIN reservations r ON p.res_id = r.res_id
          JOIN customers c ON r.customer_id = c.customer_id
          JOIN accommodations a ON r.room_id = a.room_id";

$stmt = $pdo->query($query);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>