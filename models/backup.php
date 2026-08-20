<?php

require_once '../Database/Database.php';

$database = new Database();
$conn = $database->getConnection();

$backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$tables = [];

try {
 
    $query = $conn->query("SHOW TABLES");
    while ($row = $query->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $backupData = "";

    foreach ($tables as $table) {
   
        $query = $conn->query("SHOW CREATE TABLE `$table`");
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $backupData .= "\n\n" . $row['Create Table'] . ";\n\n";

        $query = $conn->query("SELECT * FROM `$table`");
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $backupData .= "INSERT INTO `$table` VALUES (";
            $values = [];
            foreach ($row as $value) {
                $values[] = $conn->quote($value);
            }
            $backupData .= implode(", ", $values) . ");\n";
        }
    }

    // Save backup file
    file_put_contents($backupFile, $backupData);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backupFile . '"');
    readfile($backupFile);
    unlink($backupFile); // Delete after download
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
