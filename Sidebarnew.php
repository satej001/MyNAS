<?php
session_start(); // Start the session.

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['logged_in'] !== 1) {
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
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Update logged_in status
$stmt = $conn->prepare("UPDATE fileark_users SET logged_in = 1 WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileARK Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav class="nav-panel">
            <div class="logo-details">
                <i class="bx bx-archive icon"></i>
                <span class="logo_name">FileARK</span>
                <i class="bx bx-menu" id="menu-toggle" onclick="toggleMenu()"></i>
            </div>
            <ul class="nav-list">
                <li>
                </li>
                <li><a href="upload.php"><i class="bx bx-upload"></i></a></li>
                <li><a href="download.php"><i class="bx bx-download"></i></a></li>
                <li><a href="backup.php"><i class="bx bx-cloud-upload"></i></a></li>
                <li><a href="restore.php"><i class="bx bx-cloud-download"></i></a></li>
                <li><a href="MyFiles.php"><i class="bx bx-trash"></i></a></li>
                <li><a href="Logout.php"><i class="bx bx-log-out"></i></a></li>
                <li class="profile"><a href="Profile.php"><i class="bx bx-user"></i></a></li>
            </ul>
        </nav>
        <div class="content">
            <header class="welcome-header">
                <h1>Welcome to FileARK!</h1>
                <p>Select an option from the sidebar to proceed.</p>
            </header>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
