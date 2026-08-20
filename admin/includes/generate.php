<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') 
{
    header('Location: ../index.php');
    exit();
}

require_once(__DIR__ . '/../../Database/Database.php');

$first_name = $last_name = $middle_name = $gender = $dob = $contact = $address = $resident_id = $profile_picture = "";
$noResult = false;

$database = new Database();
$conn = $database->getConnection();

if (isset($_POST['generateID'])) 
{
    $resident_id = trim($_POST['resident_id']);

    if (!empty($resident_id)) 
    {
        $query = "SELECT first_name, last_name, middle_name, gender, dob, contact, address, resident_id, profile_picture FROM users WHERE resident_id = :resident_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":resident_id", $resident_id, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) 
        {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $middle_name = $row['middle_name'];
            $gender = $row['gender'];
            $dob = $row['dob'];
            $contact = $row['contact'];
            $address = $row['address'];
            $resident_id = $row['resident_id'];
            $profile_picture = $row['profile_picture'];

            $profile_picture_path = "../" . ltrim($profile_picture, '/');

        
            
            #debugg only 

            // echo $profile_picture_path;

            // if (!file_exists(__DIR__ . "/../residentID/" . $profile_picture) || empty($profile_picture)) {
            //     $profile_picture_path = "../landing/assets/images/user.png"; 
            // }
            
        } else {
            $noResult = true;
        }
    } else {
        $noResult = true;
    }
}
?>
<div class="container mt-3">
    <div class="card">
        <div class="card-body">
            <div class="form-container">
                <form action="" method="POST">
                    <div class="mb-2">
                    <label for="" class="form-label">Resident ID</label>
                    <input type="text" name="resident_id" id="resident_id" class="form-control" />
                    </div>

                    <div class="mb-2 d-flex align-items-center justify-content-center">
                        <button type="submit" class="btn btn-success mx-2" name="generateID" onclick="showCard(event)"><i class="fas fa-magic"></i> Generate</button>
                        <button id="print-card" class="btn btn-dark" onclick="printCard()"><i class="fas fa-print"></i> Print ID</button>
                    </div>
                </form>



                <div class="show-generate-resident-id-container mt-5" id="show-card" style="display: none;">
                      <div class="resident-id-card-image-container d-flex justify-content-center align-items-center">
                        <img src="../landing/assets/card-template/card-template.png" alt="" width="500" height="300" id="card-image">
                     
                        <div class="info-container">
                        <span style="text-align: center; color: #fff; margin-top: -149px; margin-left: -265px; z-index: 9999; position: absolute; font-size: 10px;">SAN JUAN RESIDENT ID CARD</span>
                        <span style="text-align: center; z-index: 9999; margin-top: -90px; margin-left: -281px; position: absolute; font-size: 25px; font-weight: bolder; color: #000;"><?= htmlspecialchars($first_name . ' ' . $last_name . ' ' . $middle_name); ?></span>
                        <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture"
                         style="border-radius: 50%; z-index: 9999; position: absolute; height: 180px; width: 180px; margin-left: -490px; margin-top: -120px;">

                        <span style="text-align: center; z-index: 9999; margin-top: 40px; margin-left: -255px; position: absolute; font-size: 15px; font-weight: lighter; color: #000;"><?= htmlspecialchars($gender) ?></span>  
                        <span style="text-align: center; z-index: 9999; margin-top: 40px; margin-left: -167px; position: absolute; font-size: 15px; font-weight: lighter; color: #000;"><?= htmlspecialchars($contact) ?></span>  
                        <span style="text-align: center; z-index: 9999; margin-top: 85px; margin-left: -270px; position: absolute; font-size: 15px; font-weight: lighter; color: #000;"><?= htmlspecialchars($dob) ?></span>
                        <span style="text-align: center; z-index: 9999; margin-top: 85px; margin-left: -170px; position: absolute; font-size: 15px; font-weight: lighter; color: #000;">
                        <?php
                                $address = htmlspecialchars($address);
                                $words = explode(' ', $address);

                                if (count($words) > 20) 
                                {
                                    $words = array_slice($words, 0, 20);
                                    $address = implode(' ', $words) . '...'; 
                                }

                                echo $address;
                            ?>

                        </span>
                        <span style="text-align: center; z-index: 9999; margin-top: 115px; margin-left: -480px; position: absolute; font-size: 15px; font-weight: lighter; color: #fff;" id="limit"><?= htmlspecialchars($resident_id) ?></span>
                    </div>
                    </div>

                
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    function showCard(event) 
    {
       
        document.getElementById('show-card').style.display = 'block';
 
    }
    showCard();

    function printCard() {
    let card = document.getElementById('show-card');
    
    card.style.display = 'block';
    let originalContent = document.body.innerHTML;
    document.body.innerHTML = card.outerHTML;
    window.print();
    document.body.innerHTML = originalContent;
}



</script>