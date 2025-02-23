<?php

session_start();

// Redirect to index.html if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // Redirect to home page
    exit();
}

$usr_id = $_SESSION['user_id'];
$baseDirs = ["/var/www/html/MyNAS/uploads/$usr_id", "/var/www/html/MyNAS/backup/$usr_id"];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file"]) && isset($_POST["folder"])) {
    $file = basename($_POST["file"]);
    $folder = rtrim($_POST["folder"], "/") . "/";
    $filePath = realpath($folder . $file);

    $isValidPath = false;

	foreach ($baseDirs as $baseDir) {
        	if($filePath !== false && strpos($filePath, $baseDir) === 0) {
                	$isValidPath = true;
			break;
        	}
	}

    if ($isValidPath && file_exists($filePath)) {
        if (unlink($filePath)) {
            header("Location: MyFiles.php?msg=File deleted successfully");
            exit();
        } else {
            header("Location: MyFiles.php?msg=Error deleting file");
            exit();
        }
    } else {
        header("Location: MyFiles.php?msg=Invalid File Path");
        exit();
    }
} else {
    header("Location: MyFiles.php");
    exit();
}
