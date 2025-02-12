<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDir = "uploads/";  // Directory to save uploaded files
    $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the directory exists, if not, create it
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Check file size (limit: 5MB)
    if ($_FILES["fileToUpload"]["size"] > 5 * 1024 * 1024) {
        $message = "❌ Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    $allowedTypes = ["jpg", "png", "pdf", "docx", "txt"];
    if (!in_array($fileType, $allowedTypes)) {
        $message = "❌ Only JPG, PNG, PDF, DOCX, and TXT files are allowed.";
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
        <h2>Upload a File</h2>
        
        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" required>
            <label for="file" id="customFileLabel">No file chosen</label>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
</body>
</html>
