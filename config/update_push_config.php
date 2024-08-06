<?php
// Include the database connection
require 'db.php';

// Initialize error and success messages
$error = '';
$success = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form inputs
    $site_name = $_POST['pushSiteName'];
    $site_url = $_POST['pushSiteUrl'];
    $site_icon = $_FILES['pushSiteIcon']['name'];
    $permission_message = $_POST['permissionMessage'];
    $allow_button_text = $_POST['permissionAllowText'];
    $deny_button_text = $_POST['permissionDenyText'];
    $welcome_title = $_POST['welcomeTitle'];
    $welcome_message = $_POST['welcomeMessage'];
    $enable_link = $_POST['enableLink'];
    $destination_link = $_POST['destinationLink'];

    // Validation: Ensure all required fields are filled
    if (empty($site_name) || empty($site_url) || empty($permission_message) || empty($allow_button_text) || empty($deny_button_text) || empty($welcome_title) || empty($welcome_message)) {
        $error = "All required fields must be filled!";
    } else {
        // If there's a file uploaded for the site icon, handle the file upload
        if (!empty($site_icon)) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES["pushSiteIcon"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if file is an actual image
            $check = getimagesize($_FILES["pushSiteIcon"]["tmp_name"]);
            if ($check === false) {
                $error = "File is not an image.";
            }

            // Check file size (limit: 2MB)
            if ($_FILES["pushSiteIcon"]["size"] > 2000000) {
                $error = "File is too large.";
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $error = "Only JPG, JPEG, and PNG files are allowed.";
            }

            // Check if there were no errors with the file
            if (empty($error)) {
                if (!move_uploaded_file($_FILES["pushSiteIcon"]["tmp_name"], $target_file)) {
                    $error = "Failed to upload the image.";
                }
            }
        }

        // If no errors, save or update the record
        if (empty($error)) {
            // Prepare the SQL query to insert or update the configuration
            $stmt = $pdo->prepare("INSERT INTO push_configurations 
                (site_name, site_url, site_icon, permission_message, allow_button_text, deny_button_text, welcome_title, welcome_message, enable_link, destination_link)
                VALUES (:site_name, :site_url, :site_icon, :permission_message, :allow_button_text, :deny_button_text, :welcome_title, :welcome_message, :enable_link, :destination_link)
                ON DUPLICATE KEY UPDATE
                site_name = VALUES(site_name),
                site_url = VALUES(site_url),
                site_icon = VALUES(site_icon),
                permission_message = VALUES(permission_message),
                allow_button_text = VALUES(allow_button_text),
                deny_button_text = VALUES(deny_button_text),
                welcome_title = VALUES(welcome_title),
                welcome_message = VALUES(welcome_message),
                enable_link = VALUES(enable_link),
                destination_link = VALUES(destination_link)");

            // Bind the parameters and execute the query
            $stmt->bindParam(':site_name', $site_name);
            $stmt->bindParam(':site_url', $site_url);
            $stmt->bindParam(':site_icon', $site_icon);
            $stmt->bindParam(':permission_message', $permission_message);
            $stmt->bindParam(':allow_button_text', $allow_button_text);
            $stmt->bindParam(':deny_button_text', $deny_button_text);
            $stmt->bindParam(':welcome_title', $welcome_title);
            $stmt->bindParam(':welcome_message', $welcome_message);
            $stmt->bindParam(':enable_link', $enable_link);
            $stmt->bindParam(':destination_link', $destination_link);

            if ($stmt->execute()) {
                $success = "Web Push configuration saved successfully!";
            } else {
                $error = "Failed to save Web Push configuration. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Save Configuration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <ion-icon name="checkmark-circle-outline"></ion-icon> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <ion-icon name="alert-circle-outline"></ion-icon> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <a href="../public/index.php" class="btn btn-primary">Back to Configuration</a>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>

