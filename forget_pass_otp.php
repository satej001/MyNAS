<?php
session_start();

/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

// Check if user email is stored in session
if (!isset($_SESSION['user_email'])) {
    header("Location: forget_pass_email.php");
    exit();
}

$user_email = $_SESSION['user_email'];

// Generate a 6-digit OTP if not already set
if (!isset($_SESSION['otp'])) {
    $_SESSION['otp'] = rand(100000, 999999);
}

// Send OTP to the user's email
$subject = "Your Password Reset OTP";
$message = "Your One-Time Password (OTP) is: " . $_SESSION['otp'] . "\n\nUse this to reset your password.";
$headers = "From: Team-FileARK";

mail($user_email, $subject, $message, $headers);

// Handle OTP Verification
$invalid_otp = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['otp'])) {
        $invalid_otp = "❌ Please enter the OTP.";
    } else {
        $entered_otp = (int) $_POST['otp'];

        if (isset($_SESSION['otp']) && $entered_otp === $_SESSION['otp']) {
            // OTP verified successfully
	    session_regenerate_id();
            unset($_SESSION['otp']); // Remove OTP from session
            $_SESSION['otp_verified'] = true; // Flag for password reset page

	    $valid_otp = "✅ OTP Verified!";

            // Redirect to reset password page
            echo "<script>setTimeout(function(){ window.location.href = 'forget_pass.php'; }, 2000);</script>";

        } else {
            $invalid_otp = "❌ Invalid OTP. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - OTP Verification</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <header>
            <h2 style="text-align: center; color: darkred;">Enter OTP</h2>
        </header>
        <p>A 6-digit OTP has been sent to your email. Please enter it below:<br><br></p>

        <?php if (!empty($invalid_otp)): ?>
            <br><p style="color: red;"><?php echo $invalid_otp; ?><br><br></p>
        <?php endif; ?>

	<?php if (!empty($valid_otp)): ?>
                <br><p style="color: green;"><?php echo $valid_otp; ?><br><br></p>
            <?php endif; ?>

        <form class="auth-form" method="POST">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit" class="btn">Verify OTP</button>
        </form>
    </div>
</body>
</html>
