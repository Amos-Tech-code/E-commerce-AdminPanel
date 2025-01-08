<?php
include('../includes/connect.php');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Ensure the required data is received via POST
if (isset($_POST['message']) && isset($_POST['user_id'])) {
    $message = trim($_POST['message']);
    $user_id = (int) $_POST['user_id'];
    $admin_id = 1; // Assuming admin ID is 1; adjust as necessary

    // Validate input
    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
        exit();
    }

        // Insert the message into the `chat_messages` table
        $insert_query = "
            INSERT INTO chat_messages (user_id, admin_id, message, sent_by_user, admin_read) 
            VALUES (?, ?, ?, 0, 1)
        ";

    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $admin_id, $message);

    if (mysqli_stmt_execute($stmt)) {
        // Retrieve messages from both tables
        $messages_query = "
            SELECT message, message_date, sent_by_user, 'chat_messages' AS source
            FROM chat_messages
            WHERE user_id = ?
            UNION ALL
            SELECT message, message_date, sent_by_user, 'contact_support' AS source
            FROM contact_support
            WHERE user_id = ?
            ORDER BY message_date ASC
        ";
        $stmt = mysqli_prepare($con, $messages_query);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
        mysqli_stmt_execute($stmt);
        $messages_result = mysqli_stmt_get_result($stmt);

        $messages = [];
        while ($row = mysqli_fetch_assoc($messages_result)) {
            $messages[] = [
                'message' => htmlspecialchars($row['message']),
                'message_date' => $row['message_date'],
                'sent_by_user' => $row['sent_by_user'],
                'source' => $row['source'] // Indicates which table the message came from
            ];
        }

        echo json_encode(['status' => 'success', 'messages' => $messages]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
