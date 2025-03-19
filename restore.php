<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}



$user_id = $_SESSION['user_id'];

$uploadDir = "backup/$user_id/"; // Folder where files are stored

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Creates the directory with full permissions if missing
}



// Check if a file is requested for download
if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']);
    $filePath = $uploadDir . $fileName;

    if (file_exists($filePath)) {
        // Set headers for file download
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));

        // Clear output buffer and send the file
        ob_clean();
        flush();
        readfile($filePath);
        exit;


    } else {
        echo "<p style='color: red;'>Error: File not found.</p>";
    }
}


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
                    <a href='restore.php?file=" . urlencode($file) . "' class='btn'>Restore</a>

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
    <title>Restore</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Available Files</h2>
        <div class="file-list">
	<?php
        // List all files in the backup directory
        if (is_dir($uploadDir) && count(array_diff(scandir($uploadDir), array('.', '..'))) > 0) {
            listFiles($uploadDir);
        } else {
            echo "<p>No Files Available</p>";
        }
        ?>

	<nav class="sidebar">
                <li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
        </nav>
    </div>
</body>
</html>
