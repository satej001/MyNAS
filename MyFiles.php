<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

error_reporting(E_ALL);
ini_set('display_errors', 1);


$uploadDir = "uploads/$user_id/"; // Folder for uploaded files
$backupDir = "backup/$user_id/"; // Folder for backuped files

// Function to list files
function listFiles($directory) {
    if (!is_dir($directory)) {
        echo "<p>No files found.</p>";
        return;
    }

    $files = array_diff(scandir($directory), array('.', '..'));
    if (empty($files)) {
        echo "<p>No files available.</p>";
    } else {
        echo "<ul>";
        foreach ($files as $file) {
            echo "<li class='file-name'>
                    <span>$file</span>
                    <form action='delete.php' method='post' style='display:inline;'>
                        <input type='hidden' name='file' value='$file'>
                        <input type='hidden' name='folder' value='$directory'>
                        <button type='submit' onclick='return confirm(\"Are you sure you want to delete $file?\")' class='btn delete-btn'>🗑 Delete</button>

		    </form>
                  </li>";
        }
        echo "</ul>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Files</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Available Files</h2>
        <?php listFiles($uploadDir); ?>
	<?php listFiles($backupDir); ?>
    </div>
	<nav class="sidebar">
                <li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
        </nav>
</body>
</html>
