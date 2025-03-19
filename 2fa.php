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

// Fetch current 2FA status
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT two_factor_enabled FROM fileark_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($two_factor_enabled);
$stmt->fetch();
$stmt->close();

// Toggle 2FA status
$new_status = ($two_factor_enabled) ? 0 : 1;
$updateStmt = $conn->prepare("UPDATE fileark_users SET two_factor_enabled = ? WHERE id = ?");
$updateStmt->bind_param("ii", $new_status, $user_id);
$updateStmt->execute();
$updateStmt->close();
$conn->close();

// Set message
$message = ($new_status) ? "<b style='color:green;'>2FA Enabled Successfully!</b>" 
                         : "<b style='color:red;'>2FA Disabled!</b>";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileARK - 2FA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="welcome-page">
        <header class="welcome-header">
            <h1 style="text-align: center;">FileARK - 2FA Status</h1>
            <p style="text-align: center;"><?php echo $message; ?></p>
            <div class="welcome-actions" style="display: flex; justify-content: center;">
                <p>Redirecting to Profile...</p>
            </div>
        </header>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "Profile.php";
        }, 3000); // Redirect after 3 seconds
    </script>
</body>
</html>
