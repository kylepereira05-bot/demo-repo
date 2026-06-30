<?php
// Database Configuration
$host = 'localhost';
$dbname = 'resort_2_0';
$username = 'root';
$password = ''; // Default XAMPP password is empty

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set Error Mode to Exception so we can see mistakes during development
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to Associative Array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, show a clean message
    die("Connection failed: " . $e->getMessage());
}
?>