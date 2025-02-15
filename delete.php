<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file"]) && isset($_POST["folder"])) {
    $file = basename($_POST["file"]);
    $folder = $_POST["folder"];
    $filePath = $folder . $file;

    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            header("Location: MyFiles.php?msg=File deleted successfully");
            exit();
        } else {
            header("Location: MyFiles.php?msg=Error deleting file");
            exit();
        }
    } else {
        header("Location: MyFiles.php?msg=File not found");
        exit();
    }
} else {
    header("Location: MyFiles.php");
    exit();
}
