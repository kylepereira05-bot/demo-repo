<?php
// Replace 'your_password_here' with the actual password you want to use
$password = 'admin123'; 
echo password_hash($password, PASSWORD_DEFAULT);
?>