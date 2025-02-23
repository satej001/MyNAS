<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$baseDir = "uploads/";
$userDir = $baseDir . $user_id . "/";

$message = ""; // Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) { // Ensure a file is selected
        $fileName = basename($_FILES["fileToUpload"]["name"]);
        $fileSize = $_FILES["fileToUpload"]["size"];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check if user directory exists, if not, create it
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }

        $targetFile = $userDir . $fileName;
        $uploadOk = 1;

        // Check file size (limit: 5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            $message = "❌ Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allowed file formats
        $allowedTypes = ["jpg", "png", "pdf", "docx", "txt", "pptx"];
        if (!in_array($fileType, $allowedTypes)) {
            $message = "❌ Only JPG, PNG, PDF, DOCX, PPTX, and TXT files are allowed.";
            $uploadOk = 0;
        }

        // Upload the file if all checks pass
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                $message = "✅ The file <strong>" . htmlspecialchars($fileName) . "</strong> has been uploaded.";

                // Database logging (Ensure $conn is properly initialized before using it)
                if (isset($conn)) {
                    $stmt = $conn->prepare("INSERT INTO fileark_logs (user_id, action, filename) VALUES (?, 'Uploaded', ?)");
                    $stmt->bind_param("is", $user_id, $fileName);
                    $stmt->execute();
                }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Upload a File<br><br></h2>
        
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form action="upload.php" method="post" enctype="multipart/form-data">
            <button type="button" class="btn" id="choose-file-btn">Choose File</button>
            <input type="file" id="file-input" name="fileToUpload" onchange="displayFileName()" hidden/>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>
	<nav class="sidebar">
		<li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
    </nav>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("file-input");
        const chooseFileBtn = document.getElementById("choose-file-btn");

        chooseFileBtn.addEventListener("click", function () {
            fileInput.click(); // Trigger file selection window
        });

        fileInput.addEventListener("change", function () {
            let fileName = fileInput.files.length > 0 ? fileInput.files[0].name : "No file chosen";
            document.querySelector(".message").innerHTML = "📂 Selected file: <strong>" + fileName + "</strong>";
        });
    });
    </script>
</body>
</html>
