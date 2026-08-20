<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

require_once('../Database/Database.php');
$db = (new Database())->getConnection();

$query = "SELECT id, profile, username, fullname, role FROM admin_tbl WHERE id = :userId LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    error_log("Failed login attempt for user ID: {$userId} at " . date("Y-m-d H:i:s"));
    $_SESSION['not-found'] = "Admin not found.";
    header('Location: ../index.php');
    exit();
}


$fullName = htmlspecialchars(trim($user['fullname']), ENT_QUOTES, 'UTF-8');
$username = htmlspecialchars(trim($user['username']), ENT_QUOTES, 'UTF-8');


$profilePicture = $user['profile_picture'] ?? null;
$filename = $profilePicture ? basename($profilePicture) : null;
$absoluteProfilePath = __DIR__ . '/../ids/' . $filename;

$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

if ($filename && in_array(strtolower($file_extension), $allowed_extensions) && file_exists($absoluteProfilePath)) {
    $profilePictureUrl = '../ids/' . $filename;
} else {
    $profilePictureUrl = '../landing/assets/images/user.png';
}


?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#06c1db">

    <title>Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="../landing/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">


    <!-- Custom fonts for this template-->
    <link href="../landing/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="../landing/assets/css/custom-script.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet">

    <!-- Load jQuery and dependencies in the head -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #20263e;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="?page=dashboard">
                <div class="sidebar-brand-icon rotate-n-15">

                </div>
                <div class="sidebar-brand-text mx-3">admin panel</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="?page=dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

<!-- Heading -->
            <div class="sidebar-heading">
                Management
            </div>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="?page=records">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Residents</span></a>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="?page=appointment">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Appointment</span></a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="?page=medrecords">
                    <i class="fas fa-fw fa-list"></i>
                    <span>Medical Records</span></a>
            </li>

            <!-- Nav Item - Charts -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="?page=generate">
                    <i class="fas fa-fw fa-magic"></i>
                    <span>Generate ID</span></a>
            </li> -->

            <li class="nav-item">
                <a class="nav-link" href="?page=addadmin">
                    <i class="fas fa-fw fa-plus"></i>
                    <span>Add Admin</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="?page=br">
                    <i class="fas fa-fw fa-database"></i>
                    <span>Backup & Restore</span></a>
            </li>

             <!-- Nav Item - Tables -->
             <li class="nav-item">
                <a class="nav-link" href="?page=account">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Account</span></a>
            </li>

             <!-- Nav Item - Tables -->
             <li class="nav-item">
                <a class="nav-link" href="../models/logoutModel.php">
                    <i class="fas fa-fw fa-arrow-right"></i>
                    <span>Logout</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                  
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                  
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($user['username']); ?></span>
                                <?php if ($user['profile']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile']); ?>" alt="Profile Picture" class="img-profile rounded-circle">
                    <?php endif; ?>
                            </a>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                       
                    </div>

                    <div class="row">

                    <?php
              
                    $allowedPages = ['dashboard', 'records', 'appointment', 'br', "account", "generate", "medrecords", "addadmin"];

           
                    $page = $_GET['page'] ?? 'dashboard';

        
                    $page = in_array($page, $allowedPages, true) ? $page : 'dashboard';

           
                    include "includes/$page.php";
                    ?>

                    </div>

                    

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <!-- <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; BMS 2025</span>
                    </div>
                </div>
            </footer> -->
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="../models/logoutModel.php" method="POST">
                        <input type="text" name="csrf_token" class="form control" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="btn btn-primary" name="logout" value="true">Logout</button>
                    </form>
                </div>
            </div>
            </div>
        </div>



    <script src="../landing/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../landing/assets/js/custom-script.min.js"></script>

</body>

</html>