<?php

require_once(__DIR__ . '/../../Database/Database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = "Admin";

    if (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $sql = "INSERT INTO admin_tbl (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            $_SESSION['success'] = "Admin added successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Failed to add admin: " . $e->getMessage();
        }
     
    } else {
        $_SESSION['error'] = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container mt-2">
    <div class="card shadow border-0">
        <div class="card-body">
        <h2 class="text-start" style="color: #000;">Add Admin</h2>
    
    <form action="" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label" style="color: #000;">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label" style="color: #000;">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Create</button>
    </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 Toast Notification -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($_SESSION['success'])): ?>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '<?php echo $_SESSION['success']; ?>',
            showConfirmButton: false,
            timer: 3000
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '<?php echo $_SESSION['error']; ?>',
            showConfirmButton: false,
            timer: 3000
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>
