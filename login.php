<?php
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
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
            <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
            <form class="auth-form" method="POST">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit" class="btn">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </div>
    </div>
</body>
</html>
