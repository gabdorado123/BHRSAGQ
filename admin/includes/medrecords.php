<?php
require_once(__DIR__ . '/../../Database/Database.php');

$db = new Database();
$conn = $db->getConnection(); 

if (!$conn) {
    die("Db failed");
}

$sql = "SELECT * FROM medical_transactions_tbl ORDER BY transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-body">
        <h2 class="text-start">Medical Records</h2>
        <div id="recordsContainer" style="overflow-x: auto;">
    <table id="residentTable" class="table table-hover" style="min-width: 1000px;">
            <thead class="text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Resident ID</th>
                    <th>Full Name</th>
                    <th>User ID</th>
                    <th>Doctor Name</th>
                    <th>Consultation Type</th>
                    <th>Appointment Date</th>
                    <th>Height</th>
                    <th>Weight</th>
                    <th>Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['resident_id']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']) ?></td>                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                        <td><?= htmlspecialchars($row['consultation_type']) ?></td>
                        <td><?= htmlspecialchars($row['appointment_date']) ?></td>
                        <td><?= htmlspecialchars($row['height']) ?></td>
                        <td><?= htmlspecialchars($row['weight']) ?></td>
                        <td><?= htmlspecialchars($row['transaction_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
        </div>
    </div>
   
</div>

<script>
$(document).ready(function() {
    var table = $('#residentTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "lengthMenu": [25, 50, 100, 500, 1000, 2000, 3000, 4000, 5000],
        "pageLength": 25,
        "dom": "<'row'<'col-sm-12 text-start'l f>>" +
               "<'row'<'col-sm-12 text-center'tr>>" +
               "<'row'<'col-sm-12 text-end'p>>"
    });

    $('#searchButton').on('click', function() {
        var residentID = $('#searchInput').val();
        table.search(residentID).draw();
    });
});
</script>

</body>
</html>
