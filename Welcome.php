<?php
session_start(); // Start the session.

if (!isset($_SESSION['user_id']) || $_SESSION['logged_in'] !== 1) {
    header("Location: login.php");
    exit();
}

require_once '/var/www/html/MyNAS/vendor/autoload.php';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <!-- Include FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="welcome-page">
        <header class="welcome-header">
            <h1 style="font-size: 40px; color:#2c3e50; text-align: center;"><b>Welcome to FileARK!</h1>
            <p style="font-size: 30px; color: darkred;"><b>Select an option from the above to proceed.</p>
        </header>
        <nav class="sidebar">
            <ul>
                <li><a href="upload.php" class="icon-link"> <span>Upload</span> <i class="fas fa-upload"></i></a></li>
                <li><a href="download.php" class="icon-link"> <span>Download</span> <i class="fas fa-download"></i></a></li>
                <li><a href="backup.php" class="icon-link"> <span>Backup</span> <i class="fas fa-cloud-upload-alt"></i></a></li>
		<li><a href="restore.php" class="icon-link"> <span>Restore</span> <i class="fas fa-cloud-download-alt"></i></a></li>
                <li><a href="MyFiles.php" class="icon-link"> <span>Delete</span> <i class="fas fa-trash"></i></a></li>
		<li><a href="Logout.php" class="icon-link"> <span>Logout</span> <i class="fas fa-sign-out-alt"></i></a></li>
            	<li><a href="Profile.php" class="profile-icon"> <i class="fa-solid fa-user"></i></a></li>
	    </ul>
        </nav>
    </div>
</body>
</html>
