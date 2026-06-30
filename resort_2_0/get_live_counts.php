<?php
require 'db.php';

// Get today's date
$today = date('Y-m-d');

// 1. Get the total number of physical rooms in the resort
$totalRoomsQuery = $pdo->query("SELECT COUNT(*) FROM accommodations");
$totalRooms = $totalRoomsQuery->fetchColumn();

// 2. Count rooms that are occupied today
$occupiedQuery = $pdo->prepare("SELECT COUNT(DISTINCT room_id) FROM reservations 
                                WHERE (check_in <= ? AND check_out > ?) 
                                AND status != 'Rejected'");
$occupiedQuery->execute([$today, $today]);
$occupiedRooms = $occupiedQuery->fetchColumn();

// 3. Calculate remaining availability
$roomsAvailableNow = $totalRooms - $occupiedRooms;
?>s