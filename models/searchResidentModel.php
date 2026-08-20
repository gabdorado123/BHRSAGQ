<?php
session_start();
require_once '../Database/Database.php';

class ResidentsModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function searchResident($searchParams) {
        $whereClause = [];
        $params = [];

        if (!empty($searchParams['resident_id'])) {
            $whereClause[] = "resident_id = :resident_id";
            $params[':resident_id'] = $searchParams['resident_id'];
        }
        if (!empty($searchParams['first_name'])) {
            $whereClause[] = "first_name LIKE :first_name";
            $params[':first_name'] = '%' . $searchParams['first_name'] . '%';
        }
        if (!empty($searchParams['last_name'])) {
            $whereClause[] = "last_name LIKE :last_name";
            $params[':last_name'] = '%' . $searchParams['last_name'] . '%';
        }
        if (!empty($searchParams['email'])) {
            $whereClause[] = "email LIKE :email";
            $params[':email'] = '%' . $searchParams['email'] . '%';
        }

        $query = "SELECT id, first_name, last_name, middle_name, resident_id, email FROM users";

        if (!empty($whereClause)) {
            $query .= " WHERE " . implode(" AND ", $whereClause);
        }

        try {
            $stmt = $this->conn->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Query Results: " . print_r($results, true));

            return $results;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
}

$resident_id = isset($_GET['resident_id']) ? $_GET['resident_id'] : '';
$first_name = isset($_GET['first_name']) ? $_GET['first_name'] : '';
$last_name = isset($_GET['last_name']) ? $_GET['last_name'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';

$residentsModel = new ResidentsModel();

$searchParams = [
    'resident_id' => $resident_id,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email
];

$results = $residentsModel->searchResident($searchParams);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $results]);