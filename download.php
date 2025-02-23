<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$uploadDir = "uploads/$user_id/"; // User-specific directory

// Ensure user's upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Check if a file is requested for download
if (isset($_GET['file'])) {
    $fileName = basename($_GET['file']); // Prevent directory traversal attacks
    $filePath = $uploadDir . $fileName;

    if (file_exists($filePath)) {
        // Log file download
        $stmt = $conn->prepare("INSERT INTO fileark_logs (user_id, action, filename) VALUES (?, 'Downloaded', ?)");
        $stmt->bind_param("is", $user_id, $fileName);
        $stmt->execute();
        $stmt->close();
        $conn->close();

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
        exit();
    } else {
        echo "<p style='color: red;'>Error: File not found.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Files</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Available Files</h2>
        <ul>
            <?php
            // List all files in the user's upload directory
            $files = array_diff(scandir($uploadDir), array('.', '..'));
            if (!empty($files)) {
                foreach ($files as $file) {
                    echo "<li class='file-name'>
                        <span style='color:darkred;'>$file</span>
                        <a href='download.php?file=" . urlencode($file) . "' class='btn'>Download</a>
                    </li>";
                }
            } else {
                echo "<p>No files available for download.</p>";
            }
            ?>
        </ul>
        <nav class="sidebar">
            <li><a href="Welcome.php" class="icon-link"> <span>FileARK</span> <i class="fas fa-home"></i></a></li>
        </nav>
    </div>
</body>
</html>
