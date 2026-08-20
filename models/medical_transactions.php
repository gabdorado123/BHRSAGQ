<?php
require_once 'config.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Transactions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Medical Transactions</h2>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>Transaction ID</th>
                <th>Resident Name</th>
                <th>Consultation Type</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $db->prepare("SELECT id, resident_name, consultation_type, date FROM medical_transactions");
            $stmt->execute();
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($transactions) {
                foreach ($transactions as $transaction) {
                    echo "<tr>
                            <td>" . htmlspecialchars($transaction['id']) . "</td>
                            <td>" . htmlspecialchars($transaction['resident_name']) . "</td>
                            <td>" . htmlspecialchars($transaction['consultation_type']) . "</td>
                            <td>" . htmlspecialchars($transaction['date']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No transactions found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
</div>

</body>
</html>