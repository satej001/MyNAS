<?php
session_start(); // Start session for CSRF token
$message = ""; // Initialize message variable

error_reporting(0); // Disable error reporting in production

require_once '/var/www/MyNAS/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/randomdirectory');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_NAME'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("CSRF validation failed for IP: " . $_SERVER['REMOTE_ADDR']);

	$_SESSION['error'] = "Your session expired. Please try again.";
	header("Location: signup.php");
	exit();
    }

    // Capture and sanitize user inputs
    $full_name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8')) : "";
    $email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

    // Basic validation
    $errors = [];

    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }

    // Password strength check
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[\W_]/', $password)) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Database connection
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error); // Log instead of displaying
            die("An error occurred. Please try again later.");
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM fileark_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "❌ This email is already registered. Please use a different email.";
        } else {
            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into database
            $stmt = $conn->prepare("INSERT INTO fileark_users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $full_name, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "✅ Successfully signed up! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                error_log("Database error: " . $stmt->error); // Log error instead of showing it
                $message = "❌ An error occurred. Please try again.";
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        $message = implode("<br>", $errors);
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - FileARK</title>
    <link rel="stylesheet" href="styles.css?v=1.0">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <h2>Sign Up</h2>
	    	<?php
			if (isset($_SESSION['error'])) {
    			echo '<p class="message" style="color: darkred;">' . $_SESSION['error'] . '</p>';
    			unset($_SESSION['error']); // Clear the message after showing
		}
		?>

            <?php if (!empty($message)): ?>
                <p class="message" style="color: <?php echo (strpos($message, '✅') !== false) ? 'green' : 'darkred'; ?>;">
                    <?php echo $message; ?>
                </p>
            <?php endif; ?>

            <form class="auth-form" action="signup.php" method="POST">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>

                <button type="submit" class="btn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
