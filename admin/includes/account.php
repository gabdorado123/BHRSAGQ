<?php

 ob_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];
    $profile = $_FILES['profile']['name'];

if (!empty($profile)) {
    $profileTmp = $_FILES['profile']['tmp_name'];
    $profilePath = "uploads/" . basename($profile); 

if (!file_exists("uploads")) {
    mkdir("uploads", 0777, true); 
    }

if (move_uploaded_file($profileTmp, $profilePath)) {
    $profilePath = "uploads/" . basename($profile); 
} else {
    $_SESSION['update-error'] = "Failed to upload the profile picture.";
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
    }
    } else {
        $profilePath = $user['profile'];
    }

    $query = "UPDATE admin_tbl SET username = :username, fullname = :fullname, role = :role, profile = :profile WHERE id = :userId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':profile', $profilePath);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['update-success'] = "Profile updated successfully!";
      
    } else {
        $_SESSION['update-error'] = "Failed to update profile.";
    }
}
ob_end_flush();
?>


<style>
    .custom-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .form-input {
        padding: 0.875rem 1rem;
        border-radius: 8px;
        border-color: #e2e8f0;
    }
    .profile-img-container {
        position: relative;
        margin-bottom: 2rem;
    }
    .profile-img {
        border: 4px solid #f8f9fa;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .custom-btn {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .custom-btn:hover {
        background-color: #1a448d;
    }
    .alert-custom {
        border-radius: 10px;
        padding: 1rem;
    }
</style>

<div class="container mt-4">
    <div class="card custom-card border-0 mb-4">
        <div class="card-body p-4 p-sm-5">
            <!-- Alerts -->
            <?php if (isset($_SESSION['update-success'])): ?>
                <div class="alert alert-success alert-custom" role="alert">
                    <?php echo $_SESSION['update-success']; unset($_SESSION['update-success']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php elseif (isset($_SESSION['update-error'])): ?>
                <div class="alert alert-danger alert-custom" role="alert">
                    <?php echo $_SESSION['update-error']; unset($_SESSION['update-error']); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="text-center mb-5">
                <h4 class="mb-1" style="color: #000;">Update Profile</h4>
                <p class="text-muted mb-3" style="color: #000;">Modify your account details</p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="needs-validation">
                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user['id']); ?>">

                <!-- Profile Image -->
                <div class="profile-img-container text-center mb-4">
                    <?php if ($user['profile']): ?>
                        <img src="<?= htmlspecialchars($user['profile']) ?>" alt="Profile Image" 
                             class="profile-img rounded-circle" width="120" height="120">
                    <?php else: ?>
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 120px; height: 120px;">
                            <i class="fas fa-user-alt text-white fa-3x"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Username -->
                <div class="form-group mb-4">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control form-input" 
                           name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <!-- Full Name -->
                <div class="form-group mb-4">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control form-input" 
                           name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                </div>

                <!-- Role -->
                <div class="form-group mb-4">
                    <label class="form-label">Role</label>
                    <select class="custom-select form-input" name="role" required>
                        <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>
                            Admin
                        </option>
                    </select>
                </div>

                <!-- File Upload -->
                <div class="form-group mb-4">
                    <label class="form-label">Profile Picture</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="profile" name="profile" 
                               aria-describedby="profileHelp">
                        <label class="custom-file-label" for="profile">Choose file</label>
                    </div>
                    <small id="profileHelp" class="form-text text-muted mt-2">
                        Recommended image size: 400x400 pixels
                    </small>
                </div>

                <div class="text-center">
                    <button type="submit" name="update" class="btn btn-primary custom-btn">
                        <i class="fas fa-sync-alt mr-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.getElementById('upload-btn').addEventListener('click', function() {
        document.getElementById('profile').click();
    });
</script>