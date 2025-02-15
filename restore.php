<?php
$uploadDir = "backup/"; // Folder where files are stored

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Files</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <h2>Available Files</h2>
        <ul>
            <?php
            // List all files in the upload directory
            $files = array_diff(scandir($uploadDir), array('.', '..'));
            if (!empty($files)) {
                foreach ($files as $file) {
                    echo "<li class='file-name'>
			<span color='white'>$file</span>
                        <a href='download.php?file=" . urlencode($file) . "' class='btn'>Download</a>
                        </li>";
                }
            } else {
                echo "<p>No files available for download.</p>";
            }
            ?>
        </ul>
    </div>
</body>
</html>
