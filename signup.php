<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form inputs
    $full_name = isset($_POST['name']) ? trim($_POST['name']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
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
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    if (!preg_match('/[\W_]/', $password)) { // \W matches any non-word character (special symbol)
        $errors[] = "Password must contain at least one special character (e.g. @#$%^&*!).";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed with database storage
    if (empty($errors)) {
        // Database connection (change credentials accordingly)
        $conn = new mysqli("localhost", "fileark_user", "#@Ck3r1sD3f3nd3r+00!", "mysql");

        // Check connection
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into database
        $stmt = $conn->prepare("INSERT INTO fileark_users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Successfully signed up! Redirecting to login...</p>";
            header("refresh:2;url=login.php"); // Redirect after 2 seconds
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        // Close connections
        $stmt->close();
        $conn->close();
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - FileARK</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <h2>Sign Up</h2>
            <form class="auth-form" action="signup.php" method="POST">
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

