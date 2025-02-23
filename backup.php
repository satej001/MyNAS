<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

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
                $message = "✅ The file <strong>" . htmlspecialchars($fileName) . "</strong> has been uploaded.";
                
                // Log upload to database
                $conn = new mysqli("localhost", "username", "password", "database"); // Replace with your DB credentials
                if ($conn->connect_error) {
                    die("Database connection failed: " . $conn->connect_error);
                }
                $stmt = $conn->prepare("INSERT INTO fileark_logs (user_id, action, filename) VALUES (?, 'Backup', ?)");
                $stmt->bind_param("is", $user_id, $fileName);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            } else {
                $message = "❌ Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $message = "❌ No file selected. Please choose a file to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup File</title>
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
            <button type="submit" class="btn">Upload</button>
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
