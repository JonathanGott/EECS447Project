<?php
session_start();

// Clear all session variables
session_unset();

// Destroy session
session_destroy();

// Send user back to login page
header("Location: login.php");
exit();
?>