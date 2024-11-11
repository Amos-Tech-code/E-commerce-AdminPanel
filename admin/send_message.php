<?php
include('../includes/connect.php');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

// Check if message and user_id are set
if (isset($_POST['message']) && isset($_POST['user_id'])) {
    $message = mysqli_real_escape_string($con, $_POST['message']);
    $user_id = $_POST['user_id'];

    // Insert the message into the database
    $insert_message_query = "INSERT INTO chat_messages (sender, receiver_id, message, message_time) 
                              VALUES ('admin', ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $insert_message_query);
    
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . mysqli_error($con)]);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
    
    // Execute the query
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Fetch the updated messages
        $messages_query = "SELECT * FROM chat_messages WHERE (sender = 'admin' AND receiver_id = ?) OR (sender = ? AND receiver_id = 'admin') ORDER BY message_time ASC";
        $stmt = mysqli_prepare($con, $messages_query);
        
        if ($stmt === false) {
            echo json_encode(['status' => 'error', 'message' => 'Query preparation failed: ' . mysqli_error($con)]);
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "is", $user_id, $user_id);
        mysqli_stmt_execute($stmt);
        $messages_result = mysqli_stmt_get_result($stmt);

        $messages = [];
        while ($row = mysqli_fetch_assoc($messages_result)) {
            $messages[] = $row;
        }

        // Return success and the updated messages
        echo json_encode(['status' => 'success', 'messages' => $messages]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message. Error: ' . mysqli_error($con)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Message or user_id is missing']);
}
?>
