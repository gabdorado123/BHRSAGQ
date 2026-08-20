<?php
session_start();
require_once '../Database/Database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['backup_file'])) {
    $database = new Database();
    $conn = $database->getConnection();

    $filePath = $_FILES['backup_file']['tmp_name'];

    if (!file_exists($filePath)) {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "No file uploaded.";
       
    }

    try {
        $sql = file_get_contents($filePath);
        $conn->exec($sql);
        $_SESSION['status'] = "success";
        $_SESSION['message'] = "Database restored successfully.";
     
    } catch (PDOException $e) {
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "Error restoring database: " . $e->getMessage();
      
    }
} else {
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Invalid request.";
   
}
