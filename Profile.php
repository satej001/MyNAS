<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '/var/www/MyNAS/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/randomdirectory');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_NAME'];

// Database connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch user's 2FA status
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT two_factor_enabled FROM fileark_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($two_factor_enabled);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <header>
            <h2 style="font-size: 40px; color: darkred; text-align: center;"><b>Welcome to Profile!</b></h2>
        </header>

	<!-- Logout -->
            <a href="Logout.php" class="btn">Logout</a>

	<!-- Reset Password -->
        <br><br><a href="resetpass.php" class="btn">Reset Password</a>

        <!-- 2FA Toggle Button -->
            <br><br><a href="2fa.php" class="btn">2 Factor Authentication</a>

        <nav class="sidebar">
            <li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
        </nav>
    </div>
</body>
</html>
