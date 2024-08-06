<?php
// Include the database connection
require 'db.php';

// Initialize error and success messages
$error = '';
$success = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form inputs
    $smtp_server_name = $_POST['smtpServer'];
    $smtp_port = $_POST['smtpPort'];
    $email_login = $_POST['emailLogin'];
    $email_password = $_POST['emailPassword'];
    $sender_name = $_POST['senderName'];
    $sender_email = $_POST['senderEmail'];

    // Validation: Ensure all required fields are filled
    if (empty($smtp_server_name) || empty($smtp_port) || empty($email_login) || empty($email_password) || empty($sender_name) || empty($sender_email)) {
        $error = "All fields are required!";
    } else {
        // Prepare the SQL query to insert or update the configuration
        $stmt = $pdo->prepare("INSERT INTO email_configuration (smtp_server_name, smtp_port, email_login, email_password, sender_name, sender_email)
            VALUES (:smtp_server_name, :smtp_port, :email_login, :email_password, :sender_name, :sender_email)
            ON DUPLICATE KEY UPDATE
            smtp_server_name = VALUES(smtp_server_name),
            smtp_port = VALUES(smtp_port),
            email_login = VALUES(email_login),
            email_password = VALUES(email_password),
            sender_name = VALUES(sender_name),
            sender_email = VALUES(sender_email)");
        
        // Bind the parameters and execute the query
        $stmt->bindParam(':smtp_server_name', $smtp_server_name);
        $stmt->bindParam(':smtp_port', $smtp_port);
        $stmt->bindParam(':email_login', $email_login);
        $stmt->bindParam(':email_password', $email_password);
        $stmt->bindParam(':sender_name', $sender_name);
        $stmt->bindParam(':sender_email', $sender_email);

        if ($stmt->execute()) {
            $success = "Email configuration saved successfully!";
        } else {
            $error = "Failed to save email configuration. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Configuration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="container mt-5">
        <!-- Display success or error message -->
        <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <ion-icon name="checkmark-circle-outline" class="me-2" style="font-size: 24px;"></ion-icon>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <ion-icon name="close-circle-outline" class="me-2" style="font-size: 24px;"></ion-icon>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Form content goes here -->
        <form action="" method="POST">
            <!-- Your email configuration form here -->
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

