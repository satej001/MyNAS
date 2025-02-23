<?php
session_start(); // Start the session

$error = ""; // Initialize error message variable

require_once '/var/www/html/MyNAS/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/randomdirectory');
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASSWORD'];
$dbName = $_ENV['DB_NAME'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form inputs
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    // Basic validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email.";
    } elseif (empty($password)) {
        $error = "Password is required.";
    } else {
        // Database connection (change credentials accordingly)
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

        // Check connection
        if ($conn->connect_error) {
            $error = "❌ Database connection failed: " . $conn->connect_error;
        } else {
            // Prepare the query to check if email exists in the database
            $stmt = $conn->prepare("SELECT id, password, two_factor_enabled FROM fileark_users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
    	   	// Email exists, now verify the password
    		$stmt->bind_result($user_id, $hashed_password, $two_factor_enabled);
    		$stmt->fetch();

    		if (password_verify($password, $hashed_password)) {
			// Authentication successful

        		$_SESSION['user_id'] = $user_id; // Store user ID in session
        		$_SESSION['email'] = $email; // Store email in session
        		$_SESSION['authenticated'] = true; // Set Authentication Flag

        		// Success message
        		$success_message = "✅ Access Granted!";
			session_regenerate_id(true);
        		if ($two_factor_enabled == 1) {
            			echo "<script>setTimeout(function() { window.location.href = '2fa_auth.php'; }, 2000);</script>";
				sleep(3);

        		} else {
				$_SESSION['logged_in'] = 1;
            			// Delay redirect with JavaScript
            			echo "<script>setTimeout(function() { window.location.href = 'Welcome.php'; }, 2000);</script>";
     			}
    		} else {
        		// Incorrect credentials (generic message for security)
        		$error = "❌ Access Denied!";
    		}
	    } else {
    		// Incorrect credentials (generic message for security)
    		$error = "❌ Access Denied!";
	    }

	   // Close the prepared statement and database connection
	   $stmt->close();
	   $conn->close();

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FileARK</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <h2>Login</h2>

		<!-- Display success message if login is successful! -->
            <?php if (!empty($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>

		 <!-- Display error message if login is failed! -->
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
            <p>Forgot your password? <a href="forget_pass_email.php">Forgot Password</a></p>
        </div>
    </div>
</body>
</html>
