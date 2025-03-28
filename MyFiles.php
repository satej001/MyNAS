<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/


$uploadDir = "uploads/$user_id/"; // Folder for uploaded files
$backupDir = "backup/$user_id/"; // Folder for backuped files

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Creates the directory with full permissions if missing
}

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true); // Creates the directory with full permissions if missing
}


function areBothDirsEmpty($uploadDir, $backupDir) {
    $uploadFiles = is_dir($uploadDir) ? array_diff(scandir($uploadDir), ['.', '..']) : [];
    $backupFiles = is_dir($backupDir) ? array_diff(scandir($backupDir), ['.', '..']) : [];
    return empty($uploadFiles) && empty($backupFiles);
}

$bothEmpty = areBothDirsEmpty($uploadDir, $backupDir);

// Function to list files
function listFiles($directory) {
    if (!is_dir($directory)) {
        return;
    }

    if(!is_dir($directory)) {
	return;
    }

    $files = array_diff(scandir($directory), array('.', '..'));
    if (empty($files)) {
        return;
    } else {
        echo "<ul>";
        foreach ($files as $file) {


	    	    $maxLength = 15;
                    $fileInfo = pathinfo($file);
                    $fileBase = $fileInfo['filename'];
                    $fileExt = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
                    if(strlen($fileBase) > $maxLength) {
                        $displayName = substr($fileBase, 0, $maxLength) . '...' . $fileExt;
                    } else {
                        $displayName = $file;
                    }

		    echo "<li class='file-name'>
                    <span>$displayName</span>
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
    <title>Delete</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Available Files</h2>
        <?php if ($bothEmpty) {
			echo "<p>No Files Available.</p>";
	      } else { ?>
		   <div class="file-list">
			<?php
				listFiles($uploadDir);
		        	listFiles($backupDir);
			?>
    	      	   </div>
	    <?php }
	      ?>
    </div>
	<nav class="sidebar">
                <li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
        </nav>
</body>
</html>
