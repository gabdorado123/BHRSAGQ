<?php

if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo ($_SESSION['status'] === 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php 
    unset($_SESSION['status']);
    unset($_SESSION['message']);
endif;
?>

<div class="container mt-3">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="text-start mx-1" style="color: #000;">Backup & Restore Database</h2>

            <div class="row">
                <!-- Backup Form Column -->
                <div class="col-md-6">
                    <h5 style="color: #000;">Backup Database</h5>
                    <form action="../models/backup.php" method="POST">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-database"></i> Backup Database
                        </button>
                    </form>
                </div>

                <!-- Restore Form Column -->
                <div class="col-md-6">
                    <h5 style="color: #000;">Restore Database</h5>
                    <form action="../models/restore.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="backup_file" class="form-control mt-2" accept=".sql" required>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-database"></i> Restore Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
