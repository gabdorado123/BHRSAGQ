<?php 

require_once(__DIR__ . '/../Database/Database.php');;

class RegisterModel
{
    private $db;
    private $targetDir;



    private $validImageTypes = ['jpg', 'jpeg', 'png'];

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        # uploaded image storage
        $this->targetDir = $_SERVER['DOCUMENT_ROOT'] . '/BHRSAGQ/residentID/';
    }

    /**
     * Registers a new user and returns the result.
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $dob
     * @param string $age
     * @param string $contact
     * @param string $civilStatus
     * @param string $gender
     * @param string $address
     * @param string $vaccination_history
     * @param string $password
     * @param array|null $profile
     * 
     * @return array
     */
    public function registerUser($email, $firstName, $lastName, $middleName, $dob, $age, $contact, $civilStatus, $gender, $address, $vaccination_history, $password, $profile)
    {
        # Check if any of the user details are already registered
        if ($this->isUserAlreadyRegistered($email, $firstName, $lastName, $middleName, $contact)) {
            return ['success' => false, 'message' => 'This user has already been registered with the same details.'];
        }

        # Validate profile image if exists
        $profilePath = $this->handleProfileImage($profile);

        # Generate Resident ID
        $residentId = $this->generateResidentId();

        # Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        # Set the role as 'Resident'
        $role = 'Resident';

        # Prepare and execute the database query
        return $this->insertUserIntoDatabase($email, $firstName, $lastName, $middleName, $dob, $age, $contact, $civilStatus, $gender, $address, $vaccination_history, $hashedPassword, $profilePath, $residentId, $role);
    }

    /**
     * Checks if any of the user details already exist in the database.
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $contact
     * 
     * @return bool
     */

    private function isUserAlreadyRegistered($email, $firstName, $lastName, $middleName, $contact)
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = :email OR (first_name = :firstName AND last_name = :lastName AND middle_name = :middleName AND contact = :contact)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':middleName', $middleName);
        $stmt->bindParam(':contact', $contact);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;  
    }

    /**
     * Handles profile image upload and returns the file path.
     *
     * @param array|null $profile
     * 
     * @return string|null
     */
    private function handleProfileImage($profile)
    {
        if (!$profile || $profile['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
    
        // residentID directory exists
        if (!is_dir($this->targetDir)) {
            mkdir($this->targetDir, 0777, true);
        }
    
        $targetFile = $this->targetDir . basename($profile['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        if (in_array($imageFileType, $this->validImageTypes) && move_uploaded_file($profile['tmp_name'], $targetFile)) {
            return 'residentID/' . basename($profile['name']);  
        }
    
        return null;
    }
    

    /**
     * Generates a unique Resident ID.
     *
     * @return string
     */
    private function generateResidentId()
    {
        $year = date("Y");
        $lastResidentId = $this->getLastResidentId();

        $newNumber = ($lastResidentId) ? $this->incrementResidentId($lastResidentId) : '001';

        return "RES-" . $year . "-" . $newNumber;
    }

    /**
     * Gets the last Resident ID from the database.
     *
     * @return string|null
     */
    private function getLastResidentId()
    {
        $query = "SELECT resident_id FROM users ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->query($query);
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['resident_id'] ?? null;
        }

        return null;
    }

    /**
     * Increments the numeric part of the last Resident ID.
     *
     * @param string $lastResidentId
     * 
     * @return string
     */
    private function incrementResidentId($lastResidentId)
    {
        preg_match('/(\d+)$/', $lastResidentId, $matches);
        $lastNumber = $matches[0] ?? 0;
        return str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Inserts the user data into the database.
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string $dob
     * @param string $age
     * @param string $contact
     * @param string $civilStatus
     * @param string $gender
     * @param string $address
     * @param string $vaccination_history
     * @param string $hashedPassword
     * @param string|null $profilePath
     * @param string $residentId
     * @param string $role
     * 
     * @return array
     */
    private function insertUserIntoDatabase($email, $firstName, $lastName, $middleName, $dob, $age, $contact, $civilStatus, $gender, $address, $vaccination_history, $hashedPassword, $profilePath, $residentId, $role)
    {
        $query = "INSERT INTO users (email, first_name, last_name, middle_name, dob, age, contact, civil_status, gender, address, vaccination_history, password, profile_picture, resident_id, role) 
                  VALUES (:email, :firstName, :lastName, :middleName, :dob, :age, :contact, :civilStatus, :gender, :address, :vaccination_history, :password, :profilePicture, :residentId, :role)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':middleName', $middleName);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':civilStatus', $civilStatus);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':vaccination_history', $vaccination_history);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':profilePicture', $profilePath);
        $stmt->bindParam(':residentId', $residentId);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful! Resident ID: ' . $residentId];
        }

        return ['success' => false, 'message' => 'Error during registration. Please try again.'];
    }
}
?>