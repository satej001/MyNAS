<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/MyNAS/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/randomdirectory');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_NAME'];

// Database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Update logged_in status
$stmt = $conn->prepare("UPDATE fileark_users SET logged_in = 0 WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->close();
$conn->close();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Delete authentication cookie if exists
if (isset($_COOKIE['user'])) {
    setcookie('user', '', time() - 3600, "/", "", true, true); // Set expiration in the past
}

// Redirect to login page
header("Location: index.html");
exit;
?>
