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
      background-color: #ffffff;
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
      font-size: 55px;
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
      padding: 0px 10px;
      text-align: left;
    }

    .log-table th {
      background-color: #f0edea;
      width: 150px; 
    }

    .log-table td {
      background-color: #ffffff;
    }

    th {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: lighter;
    }

    .log-table td:empty {
      height: 80px; 
      width: auto; 
    }

    .big-width {
      width: 100%; 
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="landing/assets/images/logo.jpg" alt="Barangay Logo" class="logo">
      <h1>Medical Appointment Log</h1>
    </div>

    <!-- Card -->
    <div class="card">
      <table class="log-table">
        <tr>
          <th>FULLNAME</th>
          <td class="big-width" colspan="3"><?php echo isset($_GET['fullname']) ? htmlspecialchars($_GET['fullname']) : ''; ?></td>
        </tr>
        <tr>
          <th>ID NUMBER</th>
          <td class="big-width" colspan="3"><?php echo isset($_GET['resident_id']) ? htmlspecialchars($_GET['resident_id']) : ''; ?></td>
        </tr>
        <tr>
          <th>TYPE OF CONSULTATION</th>
          <td colspan="3" class="big-width"><?php echo isset($_GET['consultation_type']) ? htmlspecialchars($_GET['consultation_type']) : ''; ?></td>
        </tr>
        <tr>
          <th>APPOINTMENT DATE</th>
          <td colspan="3" class="big-width"><?php echo isset($_GET['appointment_date']) ? htmlspecialchars($_GET['appointment_date']) : ''; ?></td>
        </tr>
      </table>
    </div>
  </div>
</body>
</html>
