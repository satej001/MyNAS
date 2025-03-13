<?php
session_start(); // Start the session.

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

 // Database connection (change credentials accordingly)
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Check connection
        if ($conn->connect_error) {
            $error = "❌ Database connection failed: " . $conn->connect_error;
        } else {

                // Update logged_in status
                $stmt = $conn->prepare("UPDATE fileark_users SET logged_in = 1 WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $stmt->close();
                $conn->close();
        }
?>


<!DOCTYPE html>
<!-- Website - www.codingnepalweb.com -->
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard FileARK!</title>
    <link rel="stylesheet" href="new-style.css" />
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <div class="sidebar">
      <div class="logo-details">
        <i class="bx bxs-cloud icon"></i>
        <div class="logo_name">FileARK</div>
        <i class="bx bx-menu" id="btn"></i>
      </div>
      <ul class="nav-list">
        <li>
          <a href="upload.php">
            <i class="bx bx-upload"></i>
            <span class="links_name">Upload</span>
          </a>
          <span class="tooltip">Upload</span>
        </li>
        <li>
          <a href="download.php">
            <i class="bx bx-download"></i>
            <span class="links_name">Download</span>
          </a>
          <span class="tooltip">Download</span>
        </li>
        <li>
          <a href="backup.php">
            <i class="bx bx-cloud-upload"></i>
            <span class="links_name">Backup</span>
          </a>
          <span class="tooltip">Backup</span>
        </li>
        <li>
          <a href="restore.php">
            <i class="bx bx-cloud-download"></i>
            <span class="links_name">Restore</span>
          </a>
          <span class="tooltip">Restore</span>
        </li>
        <li>
          <a href="MyFiles.php">
            <i class="bx bx-trash"></i>
            <span class="links_name">Delete</span>
          </a>
          <span class="tooltip">Delete</span>
        </li>
        <li>
          <a href="Profile.php">
            <i class="bx bx-user"></i>
            <span class="links_name">Profile</span>
          </a>
          <span class="tooltip">Profile</span>
        </li>
        <li class="profile">
	 <div class="profile-details">
	  <img src="images/ItMan.jpg" alt="profileImg" />
	  <a href="Logout.php">
	  <div class="name_job"> 
	   <div class="name" style="font-size: 20px;">Logout</div>
	   <div class="job">FileARK User</div>
	   </div>
          <i class="bx bx-log-out" id="log_out"></i>
	 </a>
        </li>
      </ul>
    </div>
    <section class="home-section">
    <div class="welcome-page">
        <header class="welcome-header">
            <h1 style="font-size: 40px; color:#2c3e50; text-align: center;"><b>Welcome to FileARK!</h1>
            <p style="font-size: 30px; color: darkred;"><b>Secure storage for all your personal digital assets!</p>
        </header>
    </div>  
    </section>

    <script src="script.js"></script>
  </body>
</html>
