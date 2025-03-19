<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

$user_id = $_SESSION['user_id'];
$backupDir = "backup/$user_id/"; // User-specific directory

// Ensure user's backup directory exists
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$message = ""; // Initialize Message Variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) { // Ensure a file is selected
        $fileSize = $_FILES["fileToUpload"]["size"];
        $fileName = basename($_FILES["fileToUpload"]["name"]);
        $targetFile = $backupDir . $fileName;
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $message = "📂 Selected file: <strong>$fileName</strong>";

        // Check file size (limit: 500 MB)
        if ($fileSize > 500 * 1024 * 1024) {
            $message = "❌ Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only certain file formats
        $allowedTypes = ["tar", "rar", "zip", "7z", "gz", "gzip"];
        if (!in_array($fileType, $allowedTypes)) {
            $message = "❌ Only TAR, RAR, ZIP, 7Z, GZ and GZIP files are allowed.";
            $uploadOk = 0;
        }

        // Check if everything is okay and upload the file
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                $message = "✅ The file " . htmlspecialchars($fileName) . " has been uploaded.";

            } else {
                $message = "❌ Sorry, there was an error backing up your file.";
            }
        }
    } else {
        $message = "❌ No file selected. Please choose a file to backup.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Backup a File</h2>
        
        <?php if (isset($message)) echo "<p class='message'>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>"; ?>

        <form action="backup.php" method="post" enctype="multipart/form-data">
            <button type="button" class="btn" id="choose-file-btn">Choose File</button>
            <input type="file" id="file-input" name="fileToUpload" onchange="displayFileName()" hidden/>
            <button type="submit" class="btn">Backup</button>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("file-input");
        const chooseFileBtn = document.getElementById("choose-file-btn");
        
        chooseFileBtn.addEventListener("click", function () {
            fileInput.click();
        });
    });

    function displayFileName() {
        let fileInput = document.getElementById("file-input");
        if (fileInput.files.length > 0) {
            document.querySelector(".message").innerHTML = "📂 Selected file: <strong>" + fileInput.files[0].name + "</strong>";
        }
    }
    </script>

    <nav class="sidebar">
        <li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
    </nav>
</body>
</html>
