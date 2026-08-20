<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../index.php');
    exit();
}

require_once(__DIR__ . '/../../Database/Database.php');
require_once(__DIR__ . '/../../models/residentsModel.php');

$database = new Database();
$conn = $database->getConnection();

$residentsModel = new ResidentsModel();
$residents = $residentsModel->getAllResidents();

#Get doctors
$doctors = [];
try {
    $stmt = $conn->prepare("SELECT id, name, position FROM doctors");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Err fetching doctors: " . $e->getMessage();
}

#Count todays completed appointments from medical_transactions_tbl
function countAppointments($conn) {
    try {
        $today = date('Y-m-d');
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM medical_transactions_tbl WHERE transaction_date = :today");
        $stmt->execute(['today' => $today]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        error_log("Err counting appointments: " . $e->getMessage());
        return 0;
    }
}

# reset slot update
if (isset($_POST['reset_slot_update'])) {
    $slotTime = $_POST['slot_time'];

    try {
        $stmt = $conn->prepare("UPDATE appointment_slots SET slot_time = :slot_time");
        $stmt->execute(['slot_time' => $slotTime]);

        $_SESSION['success'] = "Slot Reset Successfully.";
    } catch (PDOException $e) {
        error_log("Err resetting appointment slots: " . $e->getMessage());
        $_SESSION['error'] = "Failed to reset slots: " . $e->getMessage();
    }
}



# add appointment slot
if (isset($_POST['add_appointment_slot'])) {
    $slotTime = $_POST['appointment_slot'];

    try {
        #Check if the slot already exists
        $stmt_check = $conn->prepare("SELECT COUNT(*) as count FROM appointment_slots");
        $stmt_check->execute();
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($row['count'] > 0) {
            $stmt_update = $conn->prepare("UPDATE appointment_slots SET slot_time = :slot_time");
            $stmt_update->execute(['slot_time' => $slotTime]);
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO appointment_slots (slot_time) VALUES (:slot_time)");
            $stmt_insert->execute(['slot_time' => $slotTime]);
        }

        $_SESSION['success'] = "Appointment slot updated successfully.";
    } catch (PDOException $e) {
        error_log("Err adding appointment slot: " . $e->getMessage());
        $_SESSION['error'] = "Err adding appointment slot: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # mark as done
    if (isset($_POST['mark_done'])) {
        if (!isset($_POST['user_id'])) {
            $_SESSION['error'] = "Invalid request.";
        }

        $userId = $_POST['user_id'];

        try {
            // Begin transaction
            $conn->beginTransaction();
        
            // Insert into medical_transactions_tbl
            $insertStmt = $conn->prepare("
                INSERT INTO medical_transactions_tbl (
                    user_id, resident_id, first_name, last_name, middle_name, doctor_name,
                    consultation_type, appointment_date, height, weight, status, transaction_date
                )
                SELECT 
                    r.user_id, 
                    r.resident_id, 
                    u.first_name, 
                    u.last_name, 
                    u.middle_name, 
                    r.doctor_name, 
                    r.consultation_type,
                    r.appointment_date, 
                    r.height, 
                    r.weight, 
                    'completed' AS status, 
                    CURDATE()
                FROM resident_med_history r
                INNER JOIN users u ON r.user_id = u.id
                WHERE r.user_id = :user_id
            ");
            $insertStmt->execute(['user_id' => $userId]);
        
            // Delete from resident_med_history 
            $deleteStmt = $conn->prepare("
                DELETE FROM resident_med_history 
                WHERE user_id = :user_id 
            ");
            $deleteStmt->execute(['user_id' => $userId]);
        
            // Commit transaction
            $conn->commit();
        
            $_SESSION['success'] = "Appointment completed.";
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log("Err completing appointment: " . $e->getMessage());
            $_SESSION['error'] = "Err completing appointment: " . $e->getMessage();
        }
    }     

    if (isset($_POST['cancel'])) {
        if (!isset($_POST['user_id'])) {
            $_SESSION['error'] = "Invalid request.";
            return;
        }
    
        $userId = $_POST['user_id'];
    
        try {
            // Fetch appointment with user info before deletion
            $stmt = $conn->prepare("
                SELECT rm.*, u.first_name, u.middle_name, u.last_name
                FROM resident_med_history rm
                INNER JOIN users u ON rm.user_id = u.id
                WHERE rm.user_id = :user_id
            ");
            $stmt->execute(['user_id' => $userId]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$appointment) {
                $_SESSION['error'] = "Appointment not found.";
                return;
            }
    
            // Insert into cancel_appointments
            $insertStmt = $conn->prepare("
                INSERT INTO cancel_appointments (
                    appointment_number, resident_id, first_name, middle_name, last_name, doctor_name,
                    consultation_type, appointment_date, height, weight, cancelled_at
                ) VALUES (
                    :appointment_number, :resident_id, :first_name, :middle_name, :last_name, :doctor_name,
                    :consultation_type, :appointment_date, :height, :weight, NOW()
                )
            ");
    
            $insertStmt->execute([
                'appointment_number'   => $appointment['appointment_number'],
                'resident_id'          => $appointment['resident_id'],
                'first_name'           => $appointment['first_name'],
                'middle_name'          => $appointment['middle_name'],
                'last_name'            => $appointment['last_name'],
                'doctor_name'          => $appointment['doctor_name'],
                'consultation_type'    => $appointment['consultation_type'],
                'appointment_date'     => $appointment['appointment_date'],
                'height'               => $appointment['height'],
                'weight'               => $appointment['weight']
            ]);
    
            // Delete from original table
            $deleteStmt = $conn->prepare("DELETE FROM resident_med_history WHERE user_id = :user_id");
            $deleteStmt->execute(['user_id' => $userId]);
    
            $_SESSION['success'] = "Appointment cancelled and logged.";
    
        } catch (PDOException $e) {
            error_log("Error cancelling appointment: " . $e->getMessage());
            $_SESSION['error'] = "Error cancelling appointment.";
        }
    }
    
    

    #ADd doctor
    if (isset($_POST['add_doctor'])) {

        $doctorName = $_POST['doctor_name'];
        $doctorPosition = $_POST['doctor_position'];
    
        try {
            $stmt = $conn->prepare("
                INSERT INTO doctors (
                    name, position
                ) VALUES (
                    :name, :position
                )
            ");
    
            $stmt->bindParam(':name', $doctorName);
            $stmt->bindParam(':position', $doctorPosition);
    
            if ($stmt->execute()) {
                $_SESSION['success'] = "Doctor added successfully.";
            } else {
                $_SESSION['error'] = "Failed to add doctor.";
            }
    
        } catch (PDOException $e) {
            $_SESSION['error'] = "Err: " . $e->getMessage();
        }
    }
    
    
    #add appointment
    if (isset($_POST['add_appointment'])) {
        $residentId = $_POST['resident_id'];
        $doctorName = $_POST['doctor_name'];
        $consultationType = isset($_POST['consultation_type']) ? implode(',', $_POST['consultation_type']) : '';
        $appointmentDate = $_POST['appointment_date'];
        $height = $_POST['height'];
        $weight = $_POST['weight'];
        $status = 'pending'; 
        $appointment_number = $_POST['appointment_number'];
        $userId = $_POST['user_id'] ?? null; 

        try {
            #Check if user_id exists in users tbl
            $checkUserStmt = $conn->prepare("SELECT id FROM users WHERE id = :user_id");
            $checkUserStmt->execute(['user_id' => $userId]);
            if ($checkUserStmt->rowCount() === 0) {
                throw new PDOException("User ID does not exist.");
            }

            $conn->beginTransaction();

            $stmt = $conn->prepare("
                INSERT INTO resident_med_history (
                        resident_id, user_id, doctor_name, consultation_type, appointment_date, height, weight, status, appointment_number
                    ) VALUES (
                        :resident_id, :user_id, :doctor_name, :consultation_type, :appointment_date, :height, :weight, :status, :appointment_number
                    )
            ");
            
            $stmt->execute([
                'resident_id' => $residentId,
                'user_id' => $userId,
                'doctor_name' => $doctorName,
                'consultation_type' => $consultationType,
                'appointment_date' => $appointmentDate,
                'height' => $height,
                'weight' => $weight,
                'status' => $status,
                'appointment_number' => $appointment_number
            ]);

            
            // Update height and weight in users table
            $updateStmt = $conn->prepare("
                UPDATE users 
                SET height = :height, weight = :weight 
                WHERE id = :user_id
            ");
            $updateStmt->execute([
                'user_id' => $userId,
                'height' => $height,
                'weight' => $weight
            ]);

            $conn->commit();
            $_SESSION['success'] = "Appointment successfully added.";
           
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log("Err adding appointment: " . $e->getMessage());
            $_SESSION['error'] = "Err adding appointment: " . $e->getMessage();
          
        }
    }
}

#Get appointments
$query = "
    SELECT rmh.*, u.first_name, u.last_name, u.middle_name
    FROM resident_med_history rmh
    INNER JOIN users u ON rmh.user_id = u.id
    ORDER BY rmh.appointment_number ASC
";

$stmt = $conn->prepare($query);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$slot_time = 0;
try {
    $stmt_slot = $conn->prepare("SELECT slot_time FROM appointment_slots LIMIT 1");
    $stmt_slot->execute();
    $slot_row = $stmt_slot->fetch(PDO::FETCH_ASSOC);
    if ($slot_row) {
        $slot_time = $slot_row['slot_time'];
    }
} catch (PDOException $e) {
    error_log("Err get slot time: " . $e->getMessage());
}

$appointment_count = countAppointments($conn);

#Get todays completed transactions
$today = date('Y-m-d');
$completedTransactions = [];

try {
    $stmt_completed = $conn->prepare("
        SELECT mt.*, u.first_name, u.last_name, u.middle_name
        FROM medical_transactions_tbl mt
        INNER JOIN users u ON mt.user_id = u.id
        WHERE mt.transaction_date = :today
        ORDER BY mt.transaction_date DESC
    ");
    $stmt_completed->execute(['today' => $today]);
    $completedTransactions = $stmt_completed->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Err gett completed trandsac: " . $e->getMessage());
}

#Determine if the Create Appointment button is disabled
$disable_create_button = $appointment_count >= $slot_time;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Appointments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-3">
        <div class="card shadow border-0">
        <h2 class="text-start mx-1">Appointments</h2>
            <div class="container mt-1">
                <div class="d-flex justify-content-end align-items-end mb-2 mt-2">
                    <button class="btn btn-success btn-sm mx-1" 
                            data-bs-toggle="modal" 
                            data-bs-target="#addAppointmentModal"
                            <?php echo $disable_create_button ? 'disabled' : ''; ?>>
                        <i class="fas fa-plus"></i> Create Appointment
                    </button>
                    <button class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                        <i class="fas fa-plus"></i> Register Doctor
                    </button>
                    <button class="btn btn-success btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#addAppointmentSlotModal">
                        <i class="fas fa-plus"></i> Add Appointment Slot
                    </button>
                    <!-- Reset Slot Button -->
                    <button class="btn btn-success btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#resetSlotModal">
                        <i class="fas fa-undo"></i> Reset Slot
                    </button>
                                        <!-- Cancelled Appointments Button -->
                                        <button class="btn btn-success btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#cancelModal">
    Cancelled Appointments
</button>
                </div>


                <p>
                    <?php echo htmlspecialchars($slot_time); ?> Available Slots | 
                    <?php echo htmlspecialchars($appointment_count); ?> Appointments Booked
                </p>

            </div>

            <!--- Marquee Remove if not need--->
            <div class="d-flex-align-items-start justify-content-start w-25">
            <marquee behavior="" direction="">You need to reset appointment slots everyday.</marquee>
            </div>
            
            <div class="card-body">
                

                <!-- Display Success Message -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success" id="message">
                        <?php echo $_SESSION['success']; ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Display Error Message -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="d-flex-align-items-start justify-content-start mb-2 mt-1 w-25">
                        <input type="text" id="searchAppointment" placeholder="Search..." class="form-control">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead style="font-size: 12px; white-space: nowrap;">
                            <tr>
                                <th>Appointment Number</th>
                                <th style="display: none;">User ID</th>
                                <th>Resident ID</th>
                                <th>Fullname</th>
                                <th>Doctor Name</th>
                                <th>Consultation Type</th>
                                <th>Appointment Date</th>
                                <th>Height</th>
                                <th>Weight</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12px; white-space: nowrap;" id="searchAppointmentsTbl">
                        <?php if (!empty($appointments)): ?>
                            <?php 
                                $shown_resident_ids = []; #Track res ID
                            ?>
                            <?php foreach ($appointments as $appointment): ?>
                                <?php if (in_array($appointment['resident_id'], $shown_resident_ids)) continue; ?>
                                <?php $shown_resident_ids[] = $appointment['resident_id']; ?>

                                <tr>
                                    <td style="font-size: 12px; white-space: nowrap;">
                                        <button type="button" class="btn btn-primary btn-sm">
                                            Appointment # <span class="badge badge-light"><?php echo htmlspecialchars($appointment['appointment_number']); ?></span>
                                        </button>
                                    </td>
                                    <td style="font-size: 12px; white-space: nowrap; display: none;"><?php echo htmlspecialchars($appointment['user_id']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['resident_id']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name'] . ' ' . $appointment['middle_name']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['consultation_type']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['height']); ?></td>
                                    <td style="font-size: 12px; white-space: nowrap;"><?php echo htmlspecialchars($appointment['weight']); ?></td>

                                    <td style="font-size: 12px; white-space: nowrap;">
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="mark_done">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($appointment['user_id']); ?>">
                                            <button type="submit" class="btn btn-outline-success"><i class="fas fa-check"></i> Mark as Done</button>
                                        </form>

                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="cancel">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($appointment['user_id']); ?>">
                                            <button type="submit" class="btn btn-outline-danger"><i class="fa fa-times-circle"></i> Cancel</button>
                                        </form>

                                        <button class="btn btn-outline-primary btn-print"
                                            data-fullname="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name'] . ' ' . $appointment['middle_name']); ?>"
                                            data-doctor="<?php echo htmlspecialchars($appointment['doctor_name']); ?>"
                                            data-idnumber="<?php echo htmlspecialchars($appointment['resident_id']); ?>"
                                            data-consultation="<?php echo htmlspecialchars($appointment['consultation_type']); ?>"
                                            data-appointmentdate="<?php echo htmlspecialchars($appointment['appointment_date']); ?>"
                                            data-appointmentnumber="<?php echo htmlspecialchars($appointment['appointment_number']); ?>">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No appointments found today.</td>
                            </tr>
                        <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


<!-- Reset Slot Modal -->
<div class="modal fade" id="resetSlotModal" tabindex="-1" aria-labelledby="resetSlotModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reset Appointment Slots</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <p class="text-center">Are you sure you want to reset the slots?</p>
        <form action="" method="post">
          <input type="hidden" name="slot_time" value="0">
          <div class="d-flex justify-content-end">
            <button type="submit" name="reset_slot_update" class="btn btn-primary">
              <i class="fas fa-undo"></i> Reset
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Cancel Appointments Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cancelModalLabel">Cancelled Appointments</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Search bar -->
        <div class="mb-3">
          <input type="text" id="cancelSearch" class="form-control" placeholder="Search by First Name, Last Name, or Resident ID...">
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="cancelTable">
            <thead>
              <tr>
                <th>Appointment Number</th>
                <th>Resident ID</th>
                <th>Fullname</th>
                <th>Doctor Name</th>
                <th>Consultation Type</th>
                <th>Appointment Date</th>
                <th>Cancelled At</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $stmt = $conn->query("SELECT * FROM cancel_appointments ORDER BY cancelled_at DESC");
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $fullName = "{$row['first_name']} {$row['middle_name']} {$row['last_name']}";
                  echo "<tr>
                      <td>{$row['appointment_number']}</td>
                      <td>{$row['resident_id']}</td>
                      <td>{$fullName}</td>
                      <td>{$row['doctor_name']}</td>
                      <td>{$row['consultation_type']}</td>
                      <td>{$row['appointment_date']}</td>
                      <td>{$row['cancelled_at']}</td>
                  </tr>";
              }
              ?>
            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for Search Filter -->
<script>
document.getElementById("cancelSearch").addEventListener("keyup", function() {
    var input = this.value.toLowerCase();
    var rows = document.querySelectorAll("#cancelTable tbody tr");

    rows.forEach(function(row) {
        var residentId = row.cells[1].textContent.toLowerCase();
        var fullName   = row.cells[2].textContent.toLowerCase();

        if (residentId.includes(input) || fullName.includes(input)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>






 <!-- Modal for Printing -->
 <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #fff; border-bottom: none;">
                    <p class="text-start text-success">Click here to proceed <a href="#" id="printLink" style="text-decoration: underline; color: blue;"> Print.</a></p>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 0;">
                    <div class="card">
                        <div class="card-body">
                            <div class="border p-3" style="border: 2px solid #dee2e6;" id="print-medical-area">
                                <div class="d-flex align-items-center" style="background-color: #ffe6e6; padding: 10px 15px; border-bottom: 1px solid #dee2e6;" id="header-card">
                                    <img src="../landing/assets/images/logo.jpg" alt="logo" width="50" height="50" class="mr-3" style="background-color: #555; border-radius: 50%;">
                                    <h4 class="font-weight-bold text-success text-center m-0 pt-1">Medical Appointment Log</h4>
                                </div>
                                <table class="table table-borderless my-2" style="width: 100%;">
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                        <th style="width: 20%; font-weight: bold; text-align: left; padding: 8px 15px; background-color: #ffe6e6;">APPOINTMENT NUMBER</th>
                                        <td style="width: 80%; padding: 8px 15px;"><span id="modalNumber"></span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <th style="width: 20%; font-weight: bold; text-align: left; padding: 8px 15px; background-color: #ffe6e6;">FULLNAME</th>
                                        <td style="width: 80%; padding: 8px 15px;"><span id="modalFullname"></span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <th style="width: 20%; font-weight: bold; text-align: left; padding: 8px 15px; background-color: #ffe6e6;">DOCTOR</th>
                                        <td style="width: 80%; padding: 8px 15px;"><span id="modalDoctor"></span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <th style="width: 20%; font-weight: bold; text-align: left; padding: 8px 15px; background-color: #ffe6e6;">ID NUMBER</th>
                                        <td style="width: 80%; padding: 8px 15px;"><span id="modalIdNumber"></span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <th style="width: 20%; font-weight: bold; text-align: left; padding: 8px 15px; background-color: #ffe6e6;">TYPE OF CONSULTATION</th>
                                        <td style="width: 80%; padding: 8px 15px;"><span id="modalConsultation"></span></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <th style="width: 20%; font-weight: bold; text-align: left; padding: 8px 15px; background-color: #ffe6e6;">APPOINTMENT DATE</th>
                                        <td style="width: 80%; padding: 8px 15px;"><span id="modalAppointmentDate"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Appointment Slot Modal -->
    <div class="modal fade" id="addAppointmentSlotModal" tabindex="-1" aria-labelledby="addAppointmentSlotLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="appointmentSlot">Appointment Slot</label>
                            <input type="number" class="form-control" id="appointmentSlot" name="appointment_slot" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="add_appointment_slot" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDoctorModalLabel">Register Doctor</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="doctorName">Doctor Name</label>
                            <input type="text" class="form-control" id="doctorName" name="doctor_name" required>
                        </div>
                        <div class="form-group">
                            <label for="doctorPosition">Position</label>
                            <input type="text" class="form-control" id="doctorPosition" name="doctor_position" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_doctor" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Appointment -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">Create Medical Appointment</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <input type="hidden" id="residentDbId" name="user_id">
                        <div class="mb-3">
                            <label for="searchResidentID" class="form-label">Search By Resident ID</label>
                            <input type="search" name="resident_id" id="searchResidentID" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middle_name" readonly>
                        </div>
                        <div class="mb-3">
    <label for="doctorName" class="form-label">Doctor Name</label>
    <select class="form-control" id="doctorName" name="doctor_name" position="position" required>
        <option value="" selected disabled>Select a Doctor</option>
        <?php foreach ($doctors as $doctor): ?>
            <option value="<?php echo htmlspecialchars($doctor['name'] . ' - ' . $doctor['position']); ?>">
            <?php echo htmlspecialchars($doctor['name'] . ' - ' . $doctor['position']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                        <div class="mb-3">
                            <label for="consultationType" class="form-label">Consultation Type</label>
                            <div id="consultationList">
                                <div>
                                    <input type="checkbox" id="diabetes" name="consultation_type[]" value="Diabetes">
                                    <label for="diabetes">Diabetes</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="fever" name="consultation_type[]" value="Fever">
                                    <label for="fever">Fever</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="hypertension" name="consultation_type[]" value="Hypertension">
                                    <label for="hypertension">Hypertension</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="cardiology" name="consultation_type[]" value="Cardiology">
                                    <label for="cardiology">Cardiology</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="gastroenterology" name="consultation_type[]" value="Gastroenterology">
                                    <label for="gastroenterology">Gastroenterology</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="appointmentDate" class="form-label">Appointment Date</label>
                            <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
                        </div>

                        <div class="mb-3">
                            <label for="height" class="form-label">Height</label>
                            <input type="text" class="form-control" id="height" name="height" required>
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="text" class="form-control" id="weight" name="weight" required>
                        </div>

                        <div class="mb-3">
                            <label for="appointment_number" class="form-label">Appointment Number</label>
                            <input type="text" class="form-control" id="appointment_number" name="appointment_number" required>
                        </div>

                        <!-- Error Message Display -->
                        <div id="errorMessage" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_appointment" class="btn btn-primary">Save Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
          // Print Option inside Modal
          $('.btn-print').click(function(e) {
                e.preventDefault();
                var fullname = $(this).data('fullname');
                var doctor = $(this).data('doctor');
                var idNumber = $(this).data('idnumber');
                var consultation = $(this).data('consultation');
                var appointmentDate = $(this).data('appointmentdate');
                var appointmentNumber = $(this).data('appointmentnumber');

                $('#modalFullname').text(fullname);
                $('#modalDoctor').text(doctor);
                $('#modalIdNumber').text(idNumber);
                $('#modalConsultation').text(consultation);
                $('#modalAppointmentDate').text(appointmentDate);
                $('#modalNumber').text(appointmentNumber);
                
                $('#printModal').modal('show');
            });

            $('#printLink').click(function(e) {
                e.preventDefault();
                
                var printMedicalArea = $('#print-medical-area').clone();
                $('body').empty().append(printMedicalArea);
                
                window.print();
                
                location.reload();
            });

            //===> Resident ID search and auto-fill
            document.getElementById("searchResidentID").addEventListener("input", function() {
                let residentID = this.value.trim();

                if (residentID !== "") {
                    fetch(`../models/searchResidentModel.php?resident_id=${residentID}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                const resident = data.data[0];
                                document.getElementById("firstName").value = resident.first_name;
                                document.getElementById("lastName").value = resident.last_name;
                                document.getElementById("middleName").value = resident.middle_name;
                                document.getElementById("residentDbId").value = resident.id;
                            } else {
                                clearResidentFields();
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching data:", error);
                            clearResidentFields();
                        });
                } else {
                    clearResidentFields();
                }
            });

            function clearResidentFields() 
            {
            document.getElementById("firstName").value = "";
            document.getElementById("lastName").value = "";
            document.getElementById("middleName").value = "";
            document.getElementById("residentDbId").value = "";
            }

            // ===> Search Data into tbl
            document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchAppointment');
            const tableBody = document.getElementById('searchAppointmentsTbl');

            searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            const rows = tableBody.getElementsByTagName('tr');
            Array.from(rows).forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });
});

   
    </script>
</body>
</html>