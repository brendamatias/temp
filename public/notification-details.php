<?php
require_once '../config/db.php';

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $notificationId = intval($_GET['id']);

    // Fetch notification details from the database
    try {
        $stmt = $pdo->prepare('SELECT * FROM notifications WHERE id = ?');
        $stmt->execute([$notificationId]);
        $notification = $stmt->fetch();

        if ($notification) {
            // Display the notification details
            echo "<p><strong>Channel:</strong> " . htmlspecialchars($notification['channel']) . "</p>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($notification['message']) . "</p>";
            echo "<p><strong>Status:</strong> " . htmlspecialchars($notification['status']) . "</p>";
            echo "<p><strong>Created At:</strong> " . htmlspecialchars($notification['created_at']) . "</p>";
            // Add more details as needed (like confirmation of reading, etc.)
        } else {
            echo "<p>No notification found with the provided ID.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error fetching notification details: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Notification ID is missing.</p>";
}

