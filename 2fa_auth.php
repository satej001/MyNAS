<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

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

// Fetch user email
$stmt = $conn->prepare("SELECT email FROM fileark_users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($user_email);
$stmt->fetch();
$stmt->close();

// If no email is found, redirect
if (!$user_email) {
    header("Location: login.php");
    exit();
}

// Generate a 6-digit OTP
if(!isset($_SESSION['otp'])) {
	$_SESSION['otp'] = rand(100000, 999999); // Store OTP in session for verification
}

// Send OTP to user email
$subject = "Your OTP Code";
$message = "Your One-Time Password (OTP) is:" . $_SESSION['otp'] . "\n\nUse this to verify your login.";
$headers = "From: Team-FileARK";

mail($user_email, $subject, $message, $headers);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!isset($_POST['otp'])) {
	$invalid_otp = "❌ Invalid OTP. Please try again.";
}

$entered_otp = (int) $_POST['otp'];

    // Verify OTP
    if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
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

        // Remove OTP from session
        unset($_SESSION['otp']);

        // Redirect to Welcome page
        $valid_otp = "✅ OTP Verified!";
	session_regenerate_id();
	$_SESSION['logged_in'] = 1;
	echo "<script>setTimeout(function(){ window.location.href = 'Welcome.php'; }, 2000);</script>";

    } else {
        $invalid_otp = "❌ Invalid OTP. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <header>
            <h2 style="text-align: center; color: darkred;">Enter OTP</h2>
        </header>
        <p>A 6-digit OTP has been sent to your email. Please enter it below:<br><br></p>

	<?php if (!empty($invalid_otp)): ?>
                <p style="color: red;"><?php echo $invalid_otp; ?><br></p>
            <?php endif; ?>

	<?php if (!empty($valid_otp)): ?>
                <p style="color: green;"><?php echo $valid_otp; ?><br></p>
            <?php endif; ?>

        <form class="auth-form" method="POST">
            <br><input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" class="btn">Verify OTP</button>
        </form>
    </div>
</body>
</html>
