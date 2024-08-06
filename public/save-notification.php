<?php
// Fix the user ID for simplicity
$user_id = 1; // Replace with the fixed user ID

// Check if message and file were provided
if (isset($_POST['message']) && !empty($_POST['message']) && isset($_FILES['file'])) {
    $message = $_POST['message'];
    $targetDir = "../upload/";
    $fileName = basename($_FILES['file']['name']);
    $targetFile = $targetDir . $fileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        // Connect to the database
        require_once '../config/db.php'; // Ensure the DB connection file is correct

        // Prepare SQL query to insert notification
        $stmt = $db->prepare("INSERT INTO notifications (user_id, channel, message, status, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $channel = 'General'; // You can set this based on your system logic
        $status = 'Pending';

        $stmt->bind_param("isss", $user_id, $channel, $message, $status);

        if ($stmt->execute()) {
            // Return success JSON response
            echo json_encode(['success' => true, 'message' => 'Notification saved successfully', 'file_path' => $targetFile]);
        } else {
            // Return error if query fails
            echo json_encode(['success' => false, 'message' => 'Error saving notification to database']);
        }

        $stmt->close();
        $db->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Message or file not provided']);
}
?>

