<?php

require_once(__DIR__ . '/../Database/Database.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$id                 = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$email              = isset($_POST['email']) ? trim($_POST['email']) : '';
$first_name         = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name          = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$middle_name        = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
$dob                = isset($_POST['dob']) ? $_POST['dob'] : '';
$age                = isset($_POST['age']) ? $_POST['age'] : ''; // Directly from input
$contact            = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$civil_status       = isset($_POST['civilstatus']) ? trim($_POST['civilstatus']) : '';
$gender             = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$address            = isset($_POST['address']) ? trim($_POST['address']) : '';
$vaccinationHistory = isset($_POST['vaccination_history']) ? trim($_POST['vaccination_history']) : '';
$height             = isset($_POST['height']) ? trim($_POST['height']) : '';
$weight             = isset($_POST['weight']) ? trim($_POST['weight']) : '';

$profile = null;

if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../residentID/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName   = basename($_FILES['profile']['name']);
    $targetFile = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['profile']['tmp_name'], $targetFile)) {
        $profile = '/residentID/' . $fileName;
    }
}

$db = (new Database())->getConnection();

$query = "UPDATE users 
          SET email = :email,
              first_name = :first_name,
              last_name = :last_name,
              middle_name = :middle_name,
              dob = :dob,
              age = :age,
              contact = :contact,
              civil_status = :civil_status,
              gender = :gender,
              address = :address,
              vaccination_history = :vaccination_history,
              height = :height,
              weight = :weight";

if ($profile !== null) {
    $query .= ", profile_picture = :profile_picture";
}

$query .= " WHERE id = :id";

$stmt = $db->prepare($query);

$stmt->bindParam(':email', $email);
$stmt->bindParam(':first_name', $first_name);
$stmt->bindParam(':last_name', $last_name);
$stmt->bindParam(':middle_name', $middle_name);
$stmt->bindParam(':dob', $dob);
$stmt->bindParam(':age', $age);
$stmt->bindParam(':contact', $contact);
$stmt->bindParam(':civil_status', $civil_status);
$stmt->bindParam(':gender', $gender);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':vaccination_history', $vaccinationHistory);
$stmt->bindParam(':height', $height);
$stmt->bindParam(':weight', $weight);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($profile !== null) {
    $stmt->bindParam(':profile_picture', $profile);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Resident updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update resident.', 'error' => $stmt->errorInfo()]);
}
?>
