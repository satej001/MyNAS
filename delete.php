<?php

session_start();

// Redirect to index.html if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to home page
    exit();
}


$usr_id = $_SESSION['user_id'];
$baseDirs = ["/var/www/MyNAS/uploads/$usr_id", "/var/www/MyNAS/backup/$usr_id"];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file"]) && isset($_POST["folder"])) {
    $file = stripslashes($_POST["file"]);
    $file = urldecode($file);
    $file = basename($file);
    $file = str_replace("'", "\'", $file);
    $folder = rtrim($_POST["folder"], "/") . "/";
    $safeFile = escapeshellarg($file);
    $filePath = realpath($folder . $safeFile);

    $isValidPath = false;

	foreach ($baseDirs as $baseDir) {
        	if($filePath !== false && str_starts_with($filePath, $baseDir)) {
                	$isValidPath = true;
			break;
        	}
	}

    if ($isValidPath && file_exists($filePath) && is_file($filePath)) {
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
