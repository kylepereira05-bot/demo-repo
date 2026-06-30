<?php
session_start();
session_unset();
session_destroy();

// Redirect to the guest homepage after logout
header("Location: index.php");
exit();
?>s