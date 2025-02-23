<?php
session_start();
$message = ""; // Initialize message variable

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_email'])) {
    header("Location: forget_pass_email.php");
    exit();
}

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
    error_log("Database connection failed: " . $conn->connect_error);
    die("An error occurred. Please try again later.");
}

// Fetch user details
$user_email = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT password FROM fileark_users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($db_password);

if (!$stmt->fetch()) {
    error_log("No user found for email: $user_email");
    die("An error occurred. Please try again later.");
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF validation failed for IP: " . $_SERVER['REMOTE_ADDR']);
        $_SESSION['error'] = "Your session expired. Please try again.";
        header("Location: forget_pass.php");
        exit();
    }

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];


    // Prevent reusing the same password
    if (password_verify($new_password, $db_password)) {
        $errors[] = "⚠️ Please enter a different password.";
    }

    // New password validation
    if (strlen($new_password) < 8 || 
        !preg_match('/[A-Z]/', $new_password) || 
        !preg_match('/[a-z]/', $new_password) || 
        !preg_match('/[0-9]/', $new_password) || 
        !preg_match('/[\W_]/', $new_password)) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors)) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE fileark_users SET password = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $hashed_new_password, $user_email);

        if ($updateStmt->execute()) {
            $message = "✅ Password changed successfully!";
	    // Redirect to login page
            echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
        } else {
            error_log("Database error: " . $updateStmt->error);
            $message = "❌ An error occurred. Please try again.";
        }

        $updateStmt->close();
    } else {
        $message = implode("<br>", $errors);
    }
}

$conn->close();

// Generate CSRF token
if(!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FileARK</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <h2>Reset Password</h2>
            
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="message" style="color: darkred;">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
            ?>

            <?php if (!empty($message)): ?>
                <p class="message" style="color: <?php echo (strpos($message, '✅') !== false) ? 'green' : 'darkred'; ?>;">
                    <?php echo $message; ?>
                </p>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>

                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>

                <button type="submit" class="btn">Change Password</button>
            </form>
        </div>
    </div>
</body>
</html>
