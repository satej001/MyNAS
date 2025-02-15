<?php
session_start();

// Simulating user login (Replace with actual session data)
$user_logged_in = isset($_SESSION['username']) ? true : false;
$username = $user_logged_in ? $_SESSION['username'] : "Guest";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    
    <!-- FontAwesome for Icons (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Profile Icon at Top Right -->
    <div class="profile-container">
        <div class="profile-icon">
            <i class="fa-solid fa-user"></i>
        </div>
        
        <!-- Dropdown Menu -->
        <div class="profile-menu">
            <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
            <a href="profile.php"><i class="fa-solid fa-user"></i> View Profile</a>
            <a href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

</body>
</html>
