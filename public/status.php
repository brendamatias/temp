<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Database connection
require_once('../config/db.php');

// Fetch all notifications from the database
$stmt = $pdo->query("SELECT * FROM notifications");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notification Status</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- External CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Favicon -->
    <link rel="icon" href="http://localhost/notificageral/NotificaGeral/favicon.ico" type="image/x-icon">
</head>
<body>

    <!-- Header with logo and user info -->
    <header>
        <center><img src="../images/logo.png" alt="NotificaGeral Logo" style="max-width: 400px;"></center>
        <p class="lead text-center">Notifications Status Overview</p>

        <!-- User info with avatar, username, and back button -->
        <div class="user-info">
            <img src="../images/avatar.png" alt="User Avatar">
            <span class="username"><?php echo $_SESSION['username']; ?></span>
            <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-5">
        <h4 class="mb-4">Notification History</h4>

        <!-- Export buttons for PDF and Excel -->
        <div class="export-buttons mb-3">
            <a href="export.php?type=pdf" class="btn btn-danger">Export to PDF</a>
            <a href="export.php?type=excel" class="btn btn-success">Export to Excel</a>
        </div>

        <!-- Notifications table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Channel</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notification): ?>
                    <tr>
                        <td><?php echo $notification['id']; ?></td>
                        <td><?php echo $notification['channel']; ?></td>
                        <td><?php echo $notification['message']; ?></td>
                        <td>
                            <?php
                            // Set a color for the status
                            $statusColor = '';
                            switch ($notification['status']) {
                                case 'Pending':
                                    $statusColor = 'badge bg-warning';
                                    break;
                                case 'Sent':
                                    $statusColor = 'badge bg-success';
                                    break;
                                case 'Failed':
                                    $statusColor = 'badge bg-danger';
                                    break;
                            }
                            ?>
                            <span class="<?php echo $statusColor; ?>"><?php echo $notification['status']; ?></span>
                        </td>
                        <td><?php echo $notification['created_at']; ?></td>
                        <td>
                            <a href="notification-details.php?id=<?php echo $notification['id']; ?>" class="btn btn-info btn-sm">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- If no notifications found -->
        <?php if (empty($notifications)): ?>
            <p class="text-center">No notifications found.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="text-center py-4">
        <p>NotificaGeral &copy; 2024 - All Rights Reserved</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

