<?php

session_start();

require_once('models/registerModel.php');
require_once('./Database/Database.php');
require_once('models/loginModel.php');

# aler messges
$errorMessage = '';
$successMessage = '';
$loginMessage = ''; 


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $firstName = htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES, 'UTF-8');
    $lastName = htmlspecialchars($_POST['lastName'] ?? '', ENT_QUOTES, 'UTF-8');
    $middleName = htmlspecialchars($_POST['middleName'] ?? '', ENT_QUOTES, 'UTF-8');
    $dob = $_POST['dob'] ?? '';
    $contact = filter_var($_POST['contact'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $civilStatus = htmlspecialchars($_POST['civilstatus'] ?? '', ENT_QUOTES, 'UTF-8');
    $gender = htmlspecialchars($_POST['gender'] ?? '', ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $profile = $_FILES['profile'] ?? null;


    if (empty($email) || empty($firstName) || empty($lastName) || empty($dob) || empty($contact) || empty($civilStatus) || empty($gender) || empty($address) || empty($password) || empty($confirmPassword)) {
        $errorMessage = 'Fill all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Enter valid email.';
    } elseif ($password !== $confirmPassword) {
        $errorMessage = 'Passwords not match.';
    } elseif (!is_numeric($contact) || strlen($contact) < 10) {
        $errorMessage = 'Enter valid contact.';
    } else {


        $registerModel = new RegisterModel();
        $result = $registerModel->registerUser($email, $firstName, $lastName, $middleName, $dob, $age, $contact, $civilStatus, $gender, $address, $vaccination_history, $password, $profile);

        if ($result['success']) {
            $successMessage = $result['message'];
        } else {
            $errorMessage = $result['message'];
        }
    }
}


?>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $loginModel = new LoginModel();
    $loginResult = $loginModel->loginUser($email, $password, $role);

    if (!$loginResult['success']) {
        $loginMessage = $loginResult['message']; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>San Juan Barangay Management System</title>
    <link href="landing/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="landing/assets/css/front.css" />
    <link rel="stylesheet" href="landing/assets/font-awesome-4.7.0/css/font-awesome.min.css" />
</head>

<body data-bs-spy="scroll" data-bs-target="#navbarExample" data-bs-offset="70" tabindex="0">
    <!-- Navbar 1 -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow p-3 mb-5 bg-body-tertiary rounded" id="navbarExample">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="landing/assets/images/logo.jpg" alt="Logo" style="width: 40px; height: 40px; margin-right: 10px;">
                San Juan Barangay Management System
            </a>
        </div>

        
    <!-- Navbar 2 -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <ul class="navbar-nav ms-auto" id="navbarExample">
                <li class="nav-item">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#loginModal">Portal</button>
                </li>
            </ul>
        </div>
    </nav>
    </nav>

<!-- Main Content -->
<div class="container my-5">
    <!-- Hero Section -->
    <div class="row mb-4" id="homeSection">
        <div class="col-12 text-center">
            <h1>Welcome to the San Juan Barangay Management System</h1>
            <p>Your one-stop portal for managing barangay services and community affairs</p>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row card-container" id="servicesSection">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <img src="landing/assets/images/health-report.png" class="card-img-top" alt="Health Records">
                <div class="card-body">
                    <h5 class="card-title">Barangay Health Records</h5>
                    <p class="card-text">Manage health records of residents efficiently with a user-friendly system for easy tracking and access.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <img src="landing/assets/images/id.png" class="card-img-top" alt="ID Generation">
                <div class="card-body">
                    <h5 class="card-title">Automated ID Generation</h5>
                    <p class="card-text">Generate ready-to-print 3D IDs for residents, automatically filling in their details to prevent confusion and ease identification.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <img src="landing/assets/images/queue.png" class="card-img-top" alt="Queue System">
                <div class="card-body">
                    <h5 class="card-title">Queue System</h5>
                    <p class="card-text">Organize and streamline patient consultations with an automated queue system, minimizing waiting times and improving information flow.</p>
                </div>
            </div>
        </div>
    </div>
</div>


   <!-- Portal Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center fw-bolder fs-3" id="loginModalLabel">Welcome to San Juan Barangay Management System Portal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <!-- Login Form -->
                    <div class="login-container">
                        <h4>Login</h4>
                        <?php if ($errorMessage): ?>
                        <div class="alert alert-danger"><?= $errorMessage ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?= $successMessage ?></div>
                    <?php endif; ?>
                    <?php if ($loginMessage): ?>
                        <div class="alert alert-danger"><?= $loginMessage ?></div>
                    <?php endif; ?>

        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select w-25">
                                    <option value="Admin">Admin</option>
                                    <option value="Resident">Resident</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="login">Login</button>

                            <div class="mb-3 mt-2 d-flex align-items-end justify-content-end">
                            <p>Not yet member? <a href="#" onclick="showRegister()">Register</a></p>
                            </div>
                           
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div class="register-container mt-4" style="display:none;">
                        <h4>Register</h4>
                        <?php if (!empty($errorMessage)): ?>
                                    <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
                                <?php elseif (!empty($successMessage)): ?>
                                    <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                                <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                                <input type="file" class="d-none" id="profile" name="profile" accept=".png, .jpg, .jpeg" onchange="previewProfile();">
                                <!-- Button to trigger file input -->
                                <button type="button" class="btn btn-primary" onclick="document.getElementById('profile').click();">
                                    <i class="fa fa-upload"></i> Upload Profile Picture
                                </button>
                            </div>

                            <div class="d-flex align-items-center justify-content-center">
                                <img src="landing/assets/images/user.png" alt="" class="rounded-circle shadow p-3 mb-1 bg-body-tertiary rounded" width="100" height="100" id="displayprofile">
                            </div>

                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="registerEmail" name="email" placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Enter your first name">
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Enter your last name">
                            </div>

                            <div class="mb-3">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middleName" placeholder="Enter your middle name" maxlength="1" required>
                                <div id="middleNameError" class="text-danger" style="display: none;">Please enter only one uppercase letter.</div>
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob">
                            </div>

                            <div class="mb-3">
                                <label for="contact" class="form-label">Contact Number</label>
                                <input type="number" class="form-control" name="contact" id="contact">
                            </div>
                            <div class="mb-3">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <select class="form-select" id="civilStatus" name="civilstatus">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <!--- commented cuz resident ID no should be auto generated upon reg-->
                            <!-- <div class="mb-3">
                                <label for="residentId" class="form-label">Resident ID No</label>
                                <input type="text" class="form-control" id="residentId" placeholder="Enter your resident ID number">
                            </div> -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" rows="3" name="address" placeholder="Enter your address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="registerPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="register">Register</button>

                            <div class="mb-3 mt-2 d-flex align-items-end justify-content-end">
                            <p>Already a member? <a href="#" onclick="showLogin()">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
 <!--- the filename is readable and easy to understand so that you will understand easilly the purposes of each -->
    <script src="landing/assets/js/switch-portal-form.js"></script>
    <script src="landing/assets/js/preview-upload-image-registration.js"></script>
    <script src="landing/assets/js/middlename-validation.js"></script>                              
    <script src="landing/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
