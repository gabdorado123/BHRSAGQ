<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

require_once(__DIR__ . '/../../Database/Database.php');
require_once(__DIR__ . '/../../models/residentsModel.php');

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_done') {
    if (!isset($_POST['user_id'])) {
        exit(json_encode(['success' => false, 'message' => 'Invalid request']));
    }
    
    $userId = $_POST['user_id'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM resident_med_history WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $appointmentData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($appointmentData) {
            $conn->beginTransaction();
            
            try {
                $insertStmt = $conn->prepare("
                    INSERT INTO medical_transactions_tbl (
                        user_id, resident_id, doctor_name, consultation_type, 
                        appointment_date, height, weight, status
                    ) VALUES (
                        :user_id, :resident_id, :doctor_name, :consultation_type,
                        :appointment_date, :height, :weight, :status
                    )
                ");
                $insertStmt->execute([
                    'user_id' => $appointmentData['user_id'],
                    'resident_id' => $appointmentData['resident_id'],
                    'doctor_name' => $appointmentData['doctor_name'],
                    'consultation_type' => $appointmentData['consultation_type'],
                    'appointment_date' => $appointmentData['appointment_date'],
                    'height' => $appointmentData['height'],
                    'weight' => $appointmentData['weight'],
                    'status' => 'completed'
                ]);
                
                
                $conn->commit();
                exit(json_encode(['success' => true, 'message' => 'Appointment marked as done']));
            } catch (PDOException $e) {
                $conn->rollBack();
                error_log("DB Error: " . $e->getMessage());
                exit(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
            }
        } else {
            exit(json_encode(['success' => false, 'message' => 'Appointment not found']));
        }
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        exit(json_encode(['success' => false, 'message' => 'Error updating appointment status']));
    }
}