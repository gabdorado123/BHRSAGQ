<?php

session_start();

require_once('models/registerModel.php');
require_once('./Database/Database.php');
require_once('models/loginModel.php');

# aler messges
$errorMessage = '';
$successMessage = '';
$loginMessage = ''; 

?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $loginModel = new LoginModel();
    $loginResult = $loginModel->loginUser($username, $password, $role);

    if (!$loginResult['success']) {
        $loginMessage = $loginResult['message']; 
    }
}
?>
 
   <!-- Head Title -->
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay San Juan Management System</title>
    <link href="landing/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="landing/assets/css/front.css" />
    <link rel="stylesheet" href="landing/assets/font-awesome-4.7.0/css/font-awesome.min.css" />
    <style>
        .login-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        @media (max-width: 576px) {
            .login-container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-container">
                    <h4 class="text-center">Login</h4>
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"> <?= $errorMessage ?> </div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"> <?= $successMessage ?> </div>
                    <?php endif; ?>
                    <?php if ($loginMessage): ?>
                        <div class="alert alert-danger"> <?= $loginMessage ?> </div>
                    <?php endif; ?>
                    
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <input type="username" class="form-control" id="loginUsername" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="mb-3 d-none">
                            <select name="role" id="role" class="form-select">
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="landing/assets/js/switch-portal-form.js"></script>
    <script src="landing/assets/js/preview-upload-image-registration.js"></script>
    <script src="landing/assets/js/middlename-validation.js"></script>
    <script src="landing/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
