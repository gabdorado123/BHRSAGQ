<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../index.php');
    exit();
}

require_once(__DIR__ . '/../../models/registerModel.php');
require_once(__DIR__ . '/../../Database/Database.php');

$errorMessage   = '';
$successMessage = '';
$loginMessage   = '';

// Function to calculate age
function calculate_age($dob) {
  $birthDate = new DateTime($dob);
  $today = new DateTime();
  $age = $today->diff($birthDate)->y;
  return $age;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    $email       = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $firstName   = htmlspecialchars($_POST['firstName'] ?? '', ENT_QUOTES, 'UTF-8');
    $lastName    = htmlspecialchars($_POST['lastName'] ?? '', ENT_QUOTES, 'UTF-8');
    $middleName  = htmlspecialchars($_POST['middleName'] ?? '', ENT_QUOTES, 'UTF-8');
    $dob         = $_POST['dob'] ?? '';
    $contact     = filter_var($_POST['contact'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $civilStatus = htmlspecialchars($_POST['civilstatus'] ?? '', ENT_QUOTES, 'UTF-8');
    $gender      = htmlspecialchars($_POST['gender'] ?? '', ENT_QUOTES, 'UTF-8');
    $address     = htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $vaccination_history     = htmlspecialchars($_POST['vaccination_history'] ?? '', ENT_QUOTES, 'UTF-8');
   
    $password    = '';
    $profile     = $_FILES['profile'] ?? null;

    $age = calculate_age($dob);

    if (empty($email) || empty($firstName) || empty($lastName) || empty($dob) || empty($contact) || empty($civilStatus) || empty($gender) || empty($address) || empty($vaccination_history)) {
        $errorMessage = 'Fill all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Enter valid email.';
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

require_once(__DIR__ . '/../../models/residentsModel.php');
$residentsModel = new ResidentsModel();
$residents = $residentsModel->getAllResidents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Residents</title>
  
  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</head>
<body>

<div class="container mt-1">
    <div class="card shadow border-0">
  
        <div class="card-body">
        <h2 class="text-start mx-1">Barangay Residents</h2>
    <div class="mb-3 mt-2 d-flex align-items-end justify-content-end">
        <button class="btn btn-success" data-toggle="modal" data-target="#registerResidentModal">
            <i class="fas fa-plus"></i> Register
        </button>
    </div>

    <div class="mb-3">
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= $errorMessage ?></div>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if ($loginMessage): ?>
            <div class="alert alert-danger"><?= $loginMessage ?></div>
        <?php endif; ?>
    </div>

    <!-- Residents Table -->
    <div class="table-responsive">
        <table id="residentTable" class="display table table-striped">
            <thead style="background-color: #000B4F; color: #fff;">
                <tr>
                    <th style="font-size: 12px; white-space: nowrap; display: none;">#</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Resident ID</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Email</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Fullname</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Date of Birth</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Age</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Contact</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Civil Status</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Gender</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Address</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Immunizations/Vaccination History</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Height</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Weight</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Profile</th>
                    <th style="font-size: 12px; white-space: nowrap; background-color: #000B4F; color: #fff;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($residents)): ?>
                    <?php foreach ($residents as $index => $resident): ?>
                        <tr>
                            <td style="font-size: 12px; white-space: nowrap; display: none;"><?= htmlspecialchars($resident['id']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['resident_id']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['email']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['first_name']) ?> <?= htmlspecialchars($resident['last_name']) ?> <?= htmlspecialchars($resident['middle_name']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['dob']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['age']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['contact']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['civil_status']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['gender']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['address']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['vaccination_history']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['height']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;"><?= htmlspecialchars($resident['weight']) ?></td>
                            <td style="font-size: 12px; white-space: nowrap;">
                                <?php if (!empty($resident['profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($resident['profile_picture']) ?>" alt="Profile" width="50" height="50">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>       
                            <td style="font-size: 12px; white-space: nowrap;">
                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-primary" 
                                    data-toggle="modal" 
                                    data-target="#updateResidentInfoModal-<?= htmlspecialchars($resident['id']) ?>">
                              <i class="fas fa-pen"></i> Edit
                            </button>


                            <!-- Delete Button -->
                            <button class="btn btn-sm btn-danger" onclick="deleteResident(<?= htmlspecialchars($resident['id']) ?>)">
                          <i class="fas fa-trash"></i> Delete
                        </button>

                        <!--- Generate Button -->
                        <button class="btn btn-sm btn-info generate-btn" data-id="<?= htmlspecialchars($resident['id']) ?>" data-toggle="modal" data-target="#residentProfileModal">
                          <i class="fas fa-print"></i> Generate
                        </button>


                        

                        </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" class="text-center">No residents found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
        </div>
    </div>


<!-- Register Resident Modal -->
<div class="modal fade" id="registerResidentModal" tabindex="-1" aria-labelledby="registerResidentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="registerResidentModalLabel">Register Resident</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Registration Form -->
        <form id="registerResidentForm" action="" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="row">
              <div class="col-md-4 mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="middleName" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middleName" name="middleName" required>
              </div>
          </div>
          <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob" required>
          </div>
          <div class="mb-3">
            <label for="contact" class="form-label">Contact</label>
            <input type="text" class="form-control" id="contact" name="contact" maxlength="11" required>
          </div>
          <div class="row">
              <div class="col-md-6 mb-3">
                <label for="civilStatus" class="form-label">Civil Status</label>
                <select class="form-control" id="civilStatus" name="civilstatus" required>
                  <option value="">Select Civil Status</option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Widowed">Widowed</option>
                  <option value="Divorced">Divorced</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
          </div>

          <div class="mb-3">
            <label for="vaccination_history" class="form-label">Immunizations/Vaccination History</label>
            <textarea class="form-control" id="vaccination_history" name="vaccination_history" rows="2" required></textarea>
          </div>


          <div class="mb-3 d-flex align-items-center justify-content-center">
         
                <div>
                    <!-- Hidden file input -->
                    <input type="file" class="d-none" id="profile" name="profile" 
                           onchange="document.getElementById('selectedFileName').innerText = this.files[0]?.name || ''">
                    <!-- Label styled as a button -->
                    <label for="profile" class="btn btn-secondary">
                      <i class="fas fa-upload"></i> Upload Profile
                    </label>
                    <!-- Display selected file name -->
                    <span id="selectedFileName" class="ms-2"></span>
                </div>
          </div>
          <div class="mb-3 d-flex align-items-center justify-content-center">
                <button type="submit" class="btn btn-primary" name="register">Submit</button>
                </div>
               
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php foreach ($residents as $resident): ?>
<div class="modal fade" id="updateResidentInfoModal-<?= htmlspecialchars($resident['id']) ?>" tabindex="-1" 
     aria-labelledby="updateResidentInfoModalLabel-<?= htmlspecialchars($resident['id']) ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Resident - <?= htmlspecialchars($resident['first_name']) ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="../models/updateResidentModel.php" class="updateResidentForm" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= htmlspecialchars($resident['id']) ?>">
          
          <!-- Full Update Form -->
          <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" 
                   value="<?= htmlspecialchars($resident['email']) ?>" required>
          </div>
          
          <div class="row">
            <div class="col-md-4">
              <label>First Name</label>
              <input type="text" class="form-control" name="first_name" 
                     value="<?= htmlspecialchars($resident['first_name']) ?>" required>
            </div>
            <div class="col-md-4">
              <label>Last Name</label>
              <input type="text" class="form-control" name="last_name" 
                     value="<?= htmlspecialchars($resident['last_name']) ?>" required>
            </div>
            <div class="col-md-4">
              <label>Middle Initial</label>
              <input type="text" class="form-control" name="middle_name" maxlength="1"
                     value="<?= htmlspecialchars($resident['middle_name']) ?>" required>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <label>Date of Birth</label>
              <input type="date" class="form-control" name="dob" 
                     value="<?= htmlspecialchars($resident['dob']) ?>" required>
            </div>
            <div class="col-md-6">
              <label>Age</label>
              <input type="number" class="form-control" name="age"
                     value="<?= htmlspecialchars($resident['age']) ?>" required>
            </div>
          </div>
          <div class="mt-3">
            <label>Contact Number</label>
            <input type="text" class="form-control" name="contact" maxlength="11"
                   value="<?= htmlspecialchars($resident['contact']) ?>" required>
          </div>

          <div class="row mt-3">
            <div class="col-md-6">
              <label>Civil Status</label>
              <select class="form-control" name="civilstatus" required>
                <?php $statuses = ['Single', 'Married', 'Widowed', 'Divorced']; ?>
                <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $resident['civil_status'] === $status ? 'selected' : '' ?>>
                  <?= $status ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label>Gender</label>
              <select class="form-control" name="gender" required>
                <option value="Male" <?= $resident['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $resident['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
              </select>
            </div>
          </div>

          <div class="mt-3">
            <label>Address</label>
            <textarea class="form-control" name="address" rows="2" required><?= 
              htmlspecialchars($resident['address']) ?></textarea>
          </div>

          <div class="mt-3">
            <label>Immunizations/Vaccination History</label>
            <textarea class="form-control" name="vaccination_history" rows="2" required><?= 
              htmlspecialchars($resident['vaccination_history']) ?></textarea>
          </div>

          
          <div class="col-md-6">
              <label>Height</label>
              <input type="text" class="form-control" name="height"
                     value="<?= htmlspecialchars($resident['height']) ?>">
            </div>

            <div class="col-md-6">
              <label>Weight</label>
              <input type="text" class="form-control" name="weight"
                     value="<?= htmlspecialchars($resident['weight']) ?>">
            </div>
          
          



          <div class="mt-3">
            <label>Profile Picture</label>
            <input type="file" class="form-control" name="profile">
            <?php if (!empty($resident['profile_picture'])): ?>
              <div class="mt-2 d-flex justify-content-center align-items-center">
                <img src="<?= htmlspecialchars($resident['profile_picture']) ?>" 
                     alt="Current Profile" width="100" height="100" class="rounded-circle">
              </div>
            <?php endif; ?>
          </div>

          <div class="modal-footer mt-4">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>


<!--- MODAL:: GENERATE ID FOR RESIDENT ---> 
<!-- <div class="modal fade" id="residentProfileModal" tabindex="-1" aria-labelledby="residentProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <p class="modal-title text-danger" id="residentProfileModalLabel">Resident ID Card Preview</p>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body w-100"> -->
            
          <!-- <style>
            #modalProfilePicture {
              margin-top: 100px;
            }
          </style>
          <img id="modalProfilePicture" src="" alt="Profile Picture" width="100" height="100" class="rounded-circle"> -->
    

          <!-- <li class="list-group-item"><strong>Resident ID:</strong> <span id="modalResidentId"></span></li>
          <li class="list-group-item"><strong>First Name:</strong> <span id="modalFirstName"></span></li>
          <li class="list-group-item"><strong>Last Name:</strong> <span id="modalLastName"></span></li>
          <li class="list-group-item"><strong>Middle Name:</strong> <span id="modalMiddleName"></span></li>
          <li class="list-group-item"><strong>Date of Birth:</strong> <span id="modalDob"></span></li>
          <li class="list-group-item"><strong>Contact:</strong> <span id="modalContact"></span></li>
          <li class="list-group-item"><strong>Address:</strong> <span id="modalAddress"></span></li>
 -->

        <!-- <img src="../landing/assets/card-template/card-template.png" alt="" width="470" height="300" id="card-image"> -->
              
        
<!--       
      </div>
    </div>
  </div>
</div> -->

<!--- INTERNAL CSS:: GENERATE ID FOR RESIDENT ---> 
<style>
        /* Basic reset and modal styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* body {
            font-family: Arial, sans-serif;
        } */

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 800px;
            border-radius: 5px;
            overflow: hidden;
        }

        .modal-header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .modal-header a {
            color: #00897b;
            text-decoration: underline;
        }

        .modal-body {
            padding: 20px;
        }

        .close-btn {
            font-size: 24px;
            cursor: pointer;
        }


        @media print {
            body * {
                visibility: hidden;
            }
            .card-template {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 3.375in;
                height: 2.125in;
            }
            .card-header {
                width: 100%;
                height: 70px;
                background-color: #008474 !important;
            }
     
            .card-content {
                width: 100%;
            }
            .profile-section {
                display: flex;
                width: 100%;
            }
            .profile-image {
                width: 150px;
                height: 150px;
            }
            .info-section {
                width: calc(100% - 150px);
            }
            .detail-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                width: 100%;
            }
            @page {
                margin: 0;
                size: 3.375in 2.125in;
            }
        }

        .card-template {
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            width: 100%;
            max-width: 600px;
        }

        .card-header {
            background-color: #008474 !important;
            color: white;
            padding: 15px 20px;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-content {
            padding: 20px;
            background-color: white;
        }

        .profile-section {
            display: flex;
            margin-top: 20px;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 30px;
            border: 2px solid #eee;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-section {
            flex: 1;
        }

        .resident-id, .full-name {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            color: #00897b;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .detail-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
    



<!--- MODAL:: GENERATE ID FOR RESIDENT ---> 
<div class="modal fade" id="residentProfileModal" tabindex="-1" aria-labelledby="residentProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
      <p class="modal-title text-danger" id="residentProfileModalLabel">
        Click here to proceed to <a href="#" style="text-decoration: underline; color: blue;" id="printDialogShow">print</a>.
      </p>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body w-100">
        <div class="card-template" id="printable-area">
          <!-- Header Section -->
          <div class="card-header bg-teal text-white d-flex justify-content-between align-items-center py-3" style="background-color: #008474 !important;">
            <h3 class="mb-0">BARANGAY RESIDENT'S CARD</h3>
            <img src="../landing/assets/card-template/logo.png" alt="Logo" class="rounded-circle" width="30" height="30" onerror="this.style.display='none';">
          </div>
          
          <!-- Main Card Content -->
          <div class="card-body p-0">
            <!-- Profile Section -->
            <div class="row no-gutters">
              <!-- Profile Picture Column -->
              <div class="col-md-4 bg-light d-flex flex-column justify-content-between" id="modalProfileColumn">
                <div class="profile-pic-container">
                  <div class="profile-pic rounded-circle mx-auto my-3" style="width: 120px; height: 120px;">
                    <img class="text-center" id="modalProfilePicture" style="border-radius: 50%; width: 150px; height: 120px; margin-top: 20px;">
                  </div>
                </div>
                <!-- Logo Footer -->
                <!-- <div class="logo-footer text-center py-2 bg-light">
                  <small class="text-muted">
                    <div class="d-flex align-items-end justify-content-end mx-2">
                    <img src="../landing/assets/card-template/logo.png" alt="" class="rounded-circle float-end" width="30" height="30">
                    </div>
                  </small>
                </div> -->
              </div>
              
              <!-- Information Column -->
              <div class="col-md-8 p-4">
                <!-- Resident ID Section -->
                <div class="resident-id-section mb-3">
                  <h5 class="text-primary mb-1">RESIDENT ID:</h5>
                  <p class="mb-0" id="modalResidentId"></p>
                </div>
                
                <!-- Full Name Section -->
                <div class="full-name-section mb-3">
                  <h5 class="text-primary mb-1">FULL NAME:</h5>
                  <span class="mb-0" id="modalFullName"></span>
                </div>
                
                <!-- Grid for Other Details -->
                <div class="row info-grid">
                  <!-- Left Column -->
                  <div class="col-md-6">
                    <div class="info-item mb-2">
                      <h6 class="text-muted mb-1">GENDER:</h6>
                      <span class="mb-0" id="modalGender"></span>
                    </div>
                    <div class="info-item mb-2">
                      <h6 class="text-muted mb-1">DATE OF BIRTH:</h6>
                      <span class="mb-0" id="modalDob"></span>
                    </div>
                  </div>
                  
                  <!-- Right Column -->
                  <div class="col-md-6">
                    <div class="info-item mb-2">
                      <h6 class="text-muted mb-1">CONTACT:</h6>
                      <span class="mb-0" id="modalContact"></span>
                    </div>
                    <div class="info-item mb-2">
                      <h6 class="text-muted mb-1">ADDRESS:</h6>
                      <span class="mb-0" id="modalAddress"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Bootstrap 4 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>

  // ===> INITIALIZING THE DATA TABLES DEFAULT FUNCTIONS IKE (SEARCH ETC.)
    $(document).ready(function() {
        $('#residentTable').DataTable({
            "paging": true,      
            "searching": true,   
            "ordering": true,    
            "lengthMenu": [50, 100, 500, 1000, 2000, 3000, 4000, 5000], 
            "pageLength": 20,       
            "dom": "<'row'<'col-sm-12 text-left'l><'col-sm-12 text-right'f>>" +
                   "<'row'<'col-sm-12 text-center'tr>>" +
                   "<'row'<'col-sm-12 text-right'p>>"   
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
    let modalButtons = document.querySelectorAll("[data-toggle='modal']");
    modalButtons.forEach(button => {
        button.addEventListener("click", function () {
            let targetModalId = this.getAttribute("data-target");
            let modalElement = document.querySelector(targetModalId);

            if (modalElement) {
                let modalInstance = new bootstrap.Modal(modalElement);
                modalInstance.show();
            } else {
                console.error("Modal not found:", targetModalId);
            }
        });
    });
});

// Update Res

document.addEventListener('DOMContentLoaded', function() {
  const updateForms = document.querySelectorAll('.updateResidentForm');

  updateForms.forEach(function(form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
  
        showToast(data.success, data.message);
        
        if (data.success) {
          const modalElement = form.closest('.modal');
          const modalInstance = bootstrap.Modal.getInstance(modalElement);
          if (modalInstance) {
            modalInstance.hide();
          }
        }
      })
      .catch(error => {
        console.error('Err:', error);
        showToast(false, 'An err occurred while updating the resident.');
      });
    });
  });

// ===> SHOW TOAST IS BUG SO LEAVE THIS FOR NOW
  function showToast(success, message) {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.id = 'toastContainer';
      toastContainer.className = 'position-fixed bottom-0 right-0 p-3';
      toastContainer.style.zIndex = '1050';
      document.body.appendChild(toastContainer);
    }

    const toastEl = document.createElement('div');
    toastEl.className = `toast ${success ? 'bg-success' : 'bg-danger'} text-white`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
      <div class="toast-header ${success ? 'bg-success' : 'bg-danger'} text-white">
        <strong class="me-auto">${success ? 'Success' : 'Error'}</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    `;

    toastContainer.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, {
      delay: 2000
    });
    toast.show();
    window.location.reload();


    toastEl.addEventListener('hidden.bs.toast', function () {
      toastEl.remove();
    });
  }
});


// Delete Res
function deleteResident(id) {

  if (!confirm("Are you sure you want to delete this resident?")) {
    return;
  }

  const formData = new FormData();
  formData.append('id', id);

  fetch('../models/deleteResidentModel.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
  
    showToast(data.success, data.message);

    if (data.success) {
      setTimeout(() => {
        window.location.reload();
      }, 100);
    }
  })
  .catch(error => {
    console.error("Err deleting resident:", error);
    showToast(false, "An err occurred while deleting the resident.");
  });
}

function showToast(success, message) {
  let toastContainer = document.getElementById('toastContainer');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'toastContainer';
    toastContainer.className = 'position-fixed bottom-0 right-0 p-3';
    toastContainer.style.zIndex = '1050';
    document.body.appendChild(toastContainer);
  }

  const toastEl = document.createElement('div');
  toastEl.className = `toast ${success ? 'bg-success' : 'bg-danger'} text-white`;
  toastEl.setAttribute('role', 'alert');
  toastEl.setAttribute('aria-live', 'assertive');
  toastEl.setAttribute('aria-atomic', 'true');

  toastEl.innerHTML = `
    <div class="toast-header ${success ? 'bg-success' : 'bg-danger'} text-white">
      <strong class="me-auto">${success ? 'Success' : 'Error'}</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      ${message}
    </div>
  `;

  toastContainer.appendChild(toastEl);

  const toast = new bootstrap.Toast(toastEl, {
    delay: 3000
  });
  toast.show();

  toastEl.addEventListener('hidden.bs.toast', function () {
    toastEl.remove();
  });
}

// ===> DONT REMOVE CAUSE THIS WILL HELP YOU TO GET THE EXACT DATA BASED ON ID
var residentsData = <?= json_encode($residents) ?>;

// ===> THIS WILL OPEN THE MODAL AND SHOW THE DATA BASED ON ID INCLUDING GENERATE ID
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.generate-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const residentId = this.getAttribute('data-id');
      const resident = residentsData.find(r => r.id == residentId);
      
      const fullName = `${resident?.first_name || ''} ${resident?.middle_name || ''} ${resident?.last_name || ''}`
        .trim()
        .replace(/\s+/g, ' ');
      
      const fieldsToUpdate = {
        'modalProfilePicture': resident?.profile_picture || 'default.png',
        'modalResidentId': resident?.resident_id || '',
        'modalFullName': fullName || 'N/A',
        'modalGender': resident?.gender || '', 
        'modalDob': resident?.dob || '',
        'modalContact': resident?.contact || '',
        'modalAddress': resident?.address || ''
      };

      if (resident) {
        Object.entries(fieldsToUpdate).forEach(([key, value]) => {
          const element = document.getElementById(key);
          if (element) {
            if (key === 'modalProfilePicture') {
              element.src = value;
            } else {
              element.textContent = value;
            }
          }
        });
      }
    });
  });
});

// Print dialog trigger
document.getElementById('printDialogShow').addEventListener('click', function(e) {
    e.preventDefault();
    const printWindow = window.open('', '_blank', 'height=600,width=800');
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Barangay Resident's Card</title>
            <style>
          
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                    .card-header {
                    background-color: #008474 !important; 
                    color: white;
                    padding: 5px 10px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-shrink: 0;
                    box-sizing: border-box;
                }
                @media print {
                 
                     body * {
                         visibility: hidden;
                     }
                    
                     .card-template, .card-template * {
                         visibility: visible;
                     }

                      .card-header {
                         background-color: #008474 !important;
                         color: white !important; 
                         -webkit-print-color-adjust: exact;
                         print-color-adjust: exact;
                     }
                     .card-template {
                         position: absolute;
                         left: 0;
                         top: 0;
                         width: 3.375in;  
                         height: 2.125in;
                         margin: 0;
                         padding: 0;
                         box-sizing: border-box;
                         overflow: hidden; 
                     }
                   
                     @page {
                         size: 3.375in 2.125in;
                         margin: 0;
                     }
                
                     html, body {
                        overflow: hidden;
                        margin: 0;
                        padding: 0;
                     }
                }

            
                .card-template {
                    width: 3.375in;
                    height: 2.125in;
                    border: 1px solid #ddd; 
                    overflow: hidden;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1); 
                    display: flex; 
                    flex-direction: column;
                    box-sizing: border-box;
                    background-color: white;
                }
                .card-header {
                    background-color: #008474 !important;
                    color: white;
                    padding: 5px 10px; 
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-shrink: 0; 
                    box-sizing: border-box;
                }
                .card-header h3 {
                    background-color: #008474 !important;
                    color: white;
                    margin: 0;
                    font-size: 10pt; 
                    font-weight: bold;
                    white-space: nowrap;
                }

                .card-header img {
                    height: 20px; 
                    width: 20px;
                    border-radius: 50%;
                    object-fit: contain;
                }
                .card-body {
                    display: flex;
                    flex-grow: 1; 
                    box-sizing: border-box;
                    overflow: hidden; 
                }
                .profile-column {
               
                    width: 35%; 
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: flex-start; 
                    padding: 8px 5px;
                    box-sizing: border-box;
                    border-right: 1px solid #eee; 
                }
                .profile-pic {
                    width: 65px; 
                    height: 65px;
                    border-radius: 50%;
                    overflow: hidden;
                    margin-bottom: 5px;
                    flex-shrink: 0; 
                    border: 1px solid #ccc;
                }
                .profile-pic img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .info-column {
                    width: 65%;
                    padding: 5px 8px; 
                    box-sizing: border-box;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between; 
                }
                .id-section, .name-section {
                    margin-bottom: 4px;
                }
                .id-section h5, .name-section h5 {
                    margin: 0 0 1px 0;
                    color: #0275d8;
                    font-size: 7pt;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .id-section p, .name-section p {
                    margin: 0;
                    font-size: 8pt; 
                    font-weight: 600; 
                    line-height: 1.1;
                }
                .info-grid {
                    display: flex;
                    font-size: 7pt; 
                    margin-top: 4px;
                }
                .info-left, .info-right {
                    width: 50%;
                }
                 .info-left { padding-right: 3px; }
                 .info-right { padding-left: 3px; }
                .info-item {
                    margin-bottom: 3px;
                }
                .info-item h6 {
                    margin: 0 0 1px 0;
                    color: #6c757d;
                    font-size: 6.5pt; 
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .info-item span {
                    font-size: 7pt; 
                    display: block; 
                    line-height: 1.1;
                    word-wrap: break-word; 
                }
            </style>
        </head>
        <body>
            <div class="card-template">
                <!-- Header Section -->
                <div class="card-header">
                    <h3>BARANGAY RESIDENT'S CARD</h3>
                    <img src="../landing/assets/card-template/logo.png" alt="Logo" onerror="this.style.display='none';">
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Profile Picture Column -->
                    <div class="profile-column">
                        <div class="profile-pic">
                  
                            <img id="printProfilePicture" src="" alt="Resident Photo">
                        </div>
                    </div>

                    <!-- Information Column -->
                    <div class="info-column">
                        <!-- Resident ID Section -->
                        <div class="id-section">
                            <h5>RESIDENT ID:</h5>
                     
                            <p id="printResidentId"></p>
                        </div>

                        <!-- Full Name Section -->
                        <div class="name-section">
                            <h5>FULL NAME:</h5>
                            <p id="printFullName"></p>
                        </div>

                        <!-- Info Grid -->
                        <div class="info-grid">
                            <!-- Left Column -->
                            <div class="info-left">
                                <div class="info-item">
                                    <h6>GENDER:</h6>
                                    <span id="printGender"></span>
                                </div>
                                <div class="info-item">
                                    <h6>DATE OF BIRTH:</h6>
                                    <span id="printDob"></span>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="info-right">
                                <div class="info-item">
                                    <h6>CONTACT:</h6>
                                    <span id="printContact"></span>
                                </div>
                                <div class="info-item">
                                    <h6>ADDRESS:</h6>
                                    <span id="printAddress"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
    `);

    const residentData = {
        profilePicture: document.getElementById('modalProfilePicture')?.src ?? '',
        residentId: document.getElementById('modalResidentId')?.textContent ?? 'N/A',
        fullName: document.getElementById('modalFullName')?.textContent ?? 'N/A',
        gender: document.getElementById('modalGender')?.textContent ?? 'N/A',
        dob: document.getElementById('modalDob')?.textContent ?? 'N/A',
        contact: document.getElementById('modalContact')?.textContent ?? 'N/A',
        address: document.getElementById('modalAddress')?.textContent ?? 'N/A'
    };
    printWindow.document.close();
    printWindow.document.getElementById('printProfilePicture').src = residentData.profilePicture;
    printWindow.document.getElementById('printResidentId').textContent = residentData.residentId;
    printWindow.document.getElementById('printFullName').textContent = residentData.fullName;
    printWindow.document.getElementById('printGender').textContent = residentData.gender;
    printWindow.document.getElementById('printDob').textContent = residentData.dob;
    printWindow.document.getElementById('printContact').textContent = residentData.contact;
    printWindow.document.getElementById('printAddress').textContent = residentData.address;

    printWindow.document.close(); 
    printWindow.focus();

  
    setTimeout(() => {
        printWindow.print();
 
    }, 500);
   

});
        
</script>




</body>
</html>