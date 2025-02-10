<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Delete authentication cookie if exists
if (isset($_COOKIE['user'])) {
    setcookie('user', '', time() - 3600, "/"); // Set expiration in the past
}

// Redirect to login page
header("Location: login.php");
exit;
?>
