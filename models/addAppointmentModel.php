<?php
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../Database/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $resident_id = isset($_POST['resident_id']) ? htmlspecialchars(trim($_POST['resident_id'])) : '';
    $user_id = isset($_POST['user_id']) ? filter_var($_POST['user_id'], FILTER_VALIDATE_INT) : null;
    $doctor_name = isset($_POST['doctor_name']) ? htmlspecialchars(trim($_POST['doctor_name'])) : '';
    $consultation_type = isset($_POST['consultation_type']) ? htmlspecialchars(trim($_POST['consultation_type'])) : '';
    $appointment_date = isset($_POST['appointment_date']) ? htmlspecialchars(trim($_POST['appointment_date'])) : '';
    $height = isset($_POST['height']) ? htmlspecialchars(trim($_POST['height'])) : '';
    $weight = isset($_POST['weight']) ? htmlspecialchars(trim($_POST['weight'])) : '';
    $appointment_number = isset($_POST['appointment_number']) ? filter_var($_POST['appointment_number'], FILTER_VALIDATE_INT) : null;

    if (empty($resident_id) || empty($user_id) || empty($doctor_name) || empty($consultation_type) || empty($height) || empty($weight) || empty($appointment_date) || empty($appointment_number)) 
    {
        $_SESSION['error'] = "All fields are required.";
        ob_end_clean();
        exit;
    }

    if (!DateTime::createFromFormat('Y-m-d', $appointment_date)) 
    {
        $_SESSION['error'] = "Invalid date format.";
        ob_end_clean();
        exit;
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();

        $checkQuery = "SELECT id FROM users WHERE resident_id = :resident_id LIMIT 1";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':resident_id', $resident_id, PDO::PARAM_STR);
        $checkStmt->execute();

        if ($checkStmt->rowCount() == 0) 
        {
            $_SESSION['error'] = "Resident ID does not exist.";
            ob_end_clean();
            exit;
        }

        $query = "INSERT INTO resident_med_history (resident_id, user_id, doctor_name, consultation_type, appointment_date, height, weight, appointment_number) 
                  VALUES (:resident_id, :user_id, :doctor_name, :consultation_type, :appointment_date, :height, :weight, :appointment_number)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':resident_id', $resident_id, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
        $stmt->bindParam(':consultation_type', $consultation_type, PDO::PARAM_STR);
        $stmt->bindParam(':appointment_date', $appointment_date, PDO::PARAM_STR);
        $stmt->bindParam(':height', $height, PDO::PARAM_STR);
        $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
        $stmt->bindParam(':appointment_number', $appointment_number, PDO::PARAM_INT);

        if ($stmt->execute()) 
        {
            $updateQuery = "UPDATE users SET height = :height, weight = :weight WHERE id = :user_id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(':height', $height, PDO::PARAM_STR);
            $updateStmt->bindParam(':weight', $weight, PDO::PARAM_STR);
            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                $_SESSION['success'] = "success"; 
            } else {
                $_SESSION['error'] = "Err updating user details.";
            }
        } else {
            $_SESSION['error'] = "Err adding appointment.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "DB Err: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

ob_end_clean();
exit;
?>
