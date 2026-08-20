<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

require_once('../Database/Database.php');
$db = (new Database())->getConnection();

// Fetch user details from the users table based on the session user ID
$query = "SELECT * FROM users WHERE id = :userId LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Get the profile picture filename (if available)
$profilePicture = $userData['profile_picture'] ?? null;
$filename = $profilePicture ? basename($profilePicture) : null;

// Build the absolute path for file_exists() check
$absoluteProfilePath = __DIR__ . '/../ids/' . $filename;

if ($filename && file_exists($absoluteProfilePath)) {
    $profilePictureUrl = '../ids/' . $filename;
} else {
    $profilePictureUrl = '../landing/assets/images/user.png';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4">My Profile</h1>
        <div class="mb-3">
            <img src="<?php echo htmlspecialchars($profilePictureUrl); ?>" alt="Profile Picture" width="100" height="100">
        </div>
        <table class="table table-bordered">
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($userData['email']); ?></td>
            </tr>
            <tr>
                <th>First Name</th>
                <td><?php echo htmlspecialchars($userData['first_name']); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?php echo htmlspecialchars($userData['last_name']); ?></td>
            </tr>
            <tr>
                <th>Middle Name</th>
                <td><?php echo htmlspecialchars($userData['middle_name']); ?></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td><?php echo htmlspecialchars($userData['dob']); ?></td>
            </tr>
            <tr>
                <th>Contact</th>
                <td><?php echo htmlspecialchars($userData['contact']); ?></td>
            </tr>
            <tr>
                <th>Civil Status</th>
                <td><?php echo htmlspecialchars($userData['civil_status']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo htmlspecialchars($userData['gender']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($userData['address']); ?></td>
            </tr>
            <tr>
                <th>Resident ID</th>
                <td><?php echo htmlspecialchars($userData['resident_id']); ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?php echo htmlspecialchars($userData['role']); ?></td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo htmlspecialchars($userData['created_at']); ?></td>
            </tr>
        </table>
        <a href="../models/logoutModel.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
