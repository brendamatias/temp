<?php
// Start the session to get user ID
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login if not logged in
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php'; // Include the database connection file

// Retrieve the logged-in user ID
$user_id = $_SESSION['user_id'];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the channel and message from the POST request
    $channel = isset($_POST['channel']) ? $_POST['channel'] : null;
    $message = isset($_POST['message']) ? $_POST['message'] : null;

    // Validate that both channel and message are present
    if ($channel && $message) {
        // Prepare the statement to insert into the notifications table
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, channel, message, status, created_at, updated_at)
            VALUES (:user_id, :channel, :message, 'pending', NOW(), NOW())
        ");

        // Bind the parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':channel', $channel);
        $stmt->bindParam(':message', $message);

        // Execute the statement
        if ($stmt->execute()) {
            // Get the notification ID of the newly inserted row
            $notification_id = $pdo->lastInsertId();

            // Simulate the process of sending a notification (this would be more complex in a real app)
            $status = sendNotification($channel, $message) ? 'sent' : 'failed';

            // Update the notification status
            $updateStmt = $pdo->prepare("
                UPDATE notifications 
                SET status = :status, updated_at = NOW() 
                WHERE id = :notification_id
            ");
            $updateStmt->bindParam(':status', $status);
            $updateStmt->bindParam(':notification_id', $notification_id);
            $updateStmt->execute();

            // Return success response
            echo json_encode(['success' => true, 'status' => $status]);
        } else {
            // Return error response
            echo json_encode(['success' => false, 'message' => 'Failed to insert notification.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Channel or message missing.']);
    }
}

// Function to simulate sending a notification
function sendNotification($channel, $message)
{
    // Logic to send email, SMS, or Web Push
    // This would connect to services like SendGrid (Email), Twilio (SMS), or Web Push API

    switch ($channel) {
        case 'email':
            // Simulate sending email
            return true;
        case 'sms':
            // Simulate sending SMS
            return true;
        case 'web_push':
            // Simulate sending Web Push
            return true;
        default:
            return false;
    }
}

?>

