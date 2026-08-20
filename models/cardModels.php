<?php
require_once '../Database/Database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Count number of residents in users table
    $residentQuery = "SELECT COUNT(*) AS total_residents FROM users";
    $residentStmt = $conn->prepare($residentQuery);
    $residentStmt->execute();
    $residentCount = $residentStmt->fetch(PDO::FETCH_ASSOC)['total_residents'];

    // Count number of appointments in resident_med_history
    $appointmentQuery = "SELECT COUNT(*) AS total_appointments FROM resident_med_history";
    $appointmentStmt = $conn->prepare($appointmentQuery);
    $appointmentStmt->execute();
    $appointmentCount = $appointmentStmt->fetch(PDO::FETCH_ASSOC)['total_appointments'];

    // Count number of transactions in medical_transactions_tbl
    $transactionQuery = "SELECT COUNT(*) AS total_transactions FROM medical_transactions_tbl";
    $transactionStmt = $conn->prepare($transactionQuery);
    $transactionStmt->execute();
    $transactionCount = $transactionStmt->fetch(PDO::FETCH_ASSOC)['total_transactions'];

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>