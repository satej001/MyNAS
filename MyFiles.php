<?php
$uploadDir = "uploads/";
$backupDir = "backups/";

function listFiles($directory, $title) {
    echo "<h2>$title</h2>";
    
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
            $filePath = $directory . $file;
            echo "<li>📄 <strong>$file</strong> 
                    <a href='$filePath' download>⬇️ Download</a> | 
                    <form action='delete.php' method='post' style='display:inline;'>
                        <input type='hidden' name='file' value='$file'>
                        <input type='hidden' name='folder' value='$directory'>
                        <button type='submit' onclick='return confirm(\"Are you sure?\")'>🗑 Delete</button>
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>📁 My Files</h1>

        <?php listFiles($uploadDir, "Uploaded Documents & Images"); ?>
        <?php listFiles($backupDir, "Backup Files"); ?>

    </div>
</body>
</html>
