<?php
require_once(__DIR__ . '/../Database/Database.php');

class ResidentsModel
{
    private $db;
    private $targetDir;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->targetDir = '/BHRSAGQ/residentID/';
    }

    public function getAllResidents()
    {
        $query = "SELECT 
            u.id,
            u.email,
            u.first_name,
            u.last_name,
            u.middle_name,
            u.dob,
            u.age,
            u.contact,
            u.civil_status,
            u.gender,
            u.address,
            u.vaccination_history,
            u.height, 
            u.weight, 
            u.resident_id,
            u.profile_picture
        FROM users u
        ORDER BY u.resident_id ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($residents as &$resident) {
            if (!empty($resident['profile_picture'])) {
                $fileName = basename($resident['profile_picture']);
                $resident['profile_picture'] = $this->targetDir . $fileName;
            }
        }

        return $residents;
    }
}
?>
