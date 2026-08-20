<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$firstName        = isset($_GET['first_name'])        ? htmlspecialchars($_GET['first_name'])        : 'Not provided';
$middleName       = isset($_GET['middle_name'])       ? htmlspecialchars($_GET['middle_name'])       : 'Not provided';
$lastName         = isset($_GET['last_name'])         ? htmlspecialchars($_GET['last_name'])         : 'Not provided';
$residentId       = isset($_GET['resident_id'])       ? htmlspecialchars($_GET['resident_id'])       : 'Not provided';
$consultationType = isset($_GET['consultation_type']) ? htmlspecialchars($_GET['consultation_type']) : 'Not provided';
$appointmentDate  = isset($_GET['appointment_date'])  ? htmlspecialchars($_GET['appointment_date'])  : 'Not provided';

echo "<pre>";
echo "First Name: $firstName\n";
echo "Middle Name: $middleName\n";
echo "Last Name: $lastName\n";
echo "Resident ID: $residentId\n";
echo "Consultation Type: $consultationType\n";
echo "Appointment Date: $appointmentDate\n";
echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medical Appointment Log</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
    }
    .header {
      background-color: rgb(248, 231, 231);
      text-align: center;
      padding: 15px 0;
      color: green;
    }
    .header img {
      width: 60px;
      vertical-align: middle;
      margin-right: 10px;
      border-radius: 50%;
    }
    .header h1 {
      display: inline-block;
      font-size: 50px;
      margin: 0;
      vertical-align: middle;
    }
    .container {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
    }
    .card {
      background: #ffffff;
      border: none;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .log-table {
      width: 100%;
      border-collapse: collapse;
    }
    .log-table th,
    .log-table td {
      border: 1px solid #ccc;
      padding: 8px 10px;
      text-align: left;
    }
    .log-table th {
      background-color: #f0edea;
      width: 200px;
      font-weight: normal;
    }
    .log-table td {
      background-color: #ffffff;
    }
    .big-width {
      width: 100%;
    }

    @media print {
      body {
        background-color: #fff;
      }
      .container {
        max-width: 100%;
        margin: 0;
        padding: 0;
      }
      .card {
        box-shadow: none;
        margin: 0;
        padding: 0;
        border: none;
      }
      .header {
        padding: 10px 0;
      }
      .log-table th,
      .log-table td {
        font-size: 12px;
        padding: 5px;
      }
    }

    @media (max-width: 600px) {
      .header h1 {
        font-size: 35px;
      }
      .log-table th,
      .log-table td {
        font-size: 10px;
        padding: 5px;
      }
      .container {
        padding: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="../landing/assets/images/logo.jpg" alt="Barangay Logo" class="logo">
      <h1>Medical Appointment Log</h1>
    </div>

    <div class="card">
  <table class="log-table">
    <tr>
      <th>FULLNAME</th>
      <td class="big-width"><?= $firstName . ' ' . $middleName . ' ' . $lastName ?></td>
    </tr>
    <tr>
      <th>DOCTOR</th>
      <td class="big-width"><?= $doctorName ?? 'Not provided' ?></td> <!-- Add doctor name here if available -->
    </tr>
    <tr>
      <th>ID NUMBER</th>
      <td class="big-width"><?= $residentId ?></td>
    </tr>
    <tr>
      <th>TYPE OF CONSULTATION</th>
      <td class="big-width"><?= $consultationType ?></td>
    </tr>
    <tr>
      <th>APPOINTMENT DATE</th>
      <td class="big-width"><?= $appointmentDate ?></td>
    </tr>
  </table>
</div>

  </div>
</body>
</html>
