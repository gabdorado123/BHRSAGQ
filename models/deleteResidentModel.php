<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/../Database/Database.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}


$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid resident ID']);
    exit();
}

$db = (new Database())->getConnection();

$query = "DELETE FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Resident deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete resident.', 'error' => $stmt->errorInfo()]);
}
?>
