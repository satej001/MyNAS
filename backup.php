<?php

$message = ""; //Initialize Message Variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) { // Ensure a file is selected
        $targetDir = "backup/";  
        $fileSize = $_FILES["fileToUpload"]["size"];
	$targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the directory exists, if not, create it
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

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
                $message = "✅ The file <strong>" . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . "</strong> has been uploaded.";
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
    <title>Upload File</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Backup a File</h2>
        
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <form action="backup.php" method="post" enctype="multipart/form-data">
            <button type="button" class="btn" id="choose-file-btn">Choose File</button>
            <input type="file" id="file-input" name="fileToUpload" onchange="displayFileName()" hidden/>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>

    <script>
    // Ensure script runs after the DOM loads
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("file-input");
        const chooseFileBtn = document.getElementById("choose-file-btn");
        const fileChosenText = document.getElementById("file-chosen");

        chooseFileBtn.addEventListener("click", function () {
            fileInput.click(); // Trigger file selection window
        });

        fileInput.addEventListener("change", function () {
            fileChosenText.textContent = this.files.length > 0 ? this.files[0].name : "No file chosen";
        });
    });

	function displayFileName() {
        let fileInput = document.getElementById("file-input");
        if (fileInput.files.length > 0) {
            document.querySelector(".message").innerHTML = "📂 Selected file: <strong>" + fileInput.files[0].name + "</strong>";
        }
    }

    </script>
</body>
</html>
