<?php
session_start();


$invalid_email = "";

require_once '/var/www/MyNAS/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/randomdirectory');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_NAME'];

 // Database connection (change credentials accordingly)
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT id FROM fileark_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists, Store it in the session & redirect to forget_pass.php
	$_SESSION['user_email'] = $email;
        header("Location: forget_pass_otp.php");
        exit();
    } else {
        // Email does not exist, show an error message
        $invalid_email = "❌ Please enter a valid email!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <header>
            <h2 style="text-align: center; color: darkred;">Forgot Password</h2>
        </header>
        <p>Enter your registered email to reset your password:<br><br></p>

        <?php if (!empty($invalid_email)): ?>
            <p style="color: red;"><?php echo $invalid_email; ?><br><br></p>
        <?php endif; ?>

        <form class="auth-form" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</body>
</html>
