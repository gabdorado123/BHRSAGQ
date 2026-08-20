<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

require_once(__DIR__ . '/../../Database/Database.php');
require_once(__DIR__ . '/../../models/residentsModel.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    if (!isset($_POST['user_id'])) {
        exit(json_encode(['success' => false, 'message' => 'Invalid request']));
    }
    
    $userId = $_POST['user_id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM resident_med_history WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        exit(json_encode(['success' => true, 'message' => 'Appointment cancelled']));
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        exit(json_encode(['success' => false, 'message' => 'Error cancelling appointment']));
    }
}