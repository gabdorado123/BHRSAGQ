<?php

require_once('./Database/Database.php');

class LoginModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Handle user login based on role (Admin/Resident)
     * @param string $useranme
     * @param string $password
     * @param string $role
     * @return mixed
     */
    public function loginUser($username, $password, $role)
    {
        
        $table = ($role === 'Admin') ? 'admin_tbl' : 'users';
        
  
        $query = "SELECT * FROM $table WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
         
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? null; 

            if ($role === 'Admin') {
                header("Location: ./admin/dashboard.php");
            } else {
                header("Location: ./residents/dashboard.php");
            }
            exit();
        }

        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
}
?>
