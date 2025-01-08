<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Fetch message_id and user_id from URL parameters
if (isset($_GET['user_id'], $_GET['message_id']) && !empty($_GET['user_id']) && !empty($_GET['message_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id
    $message_id = intval($_GET['message_id']); // Sanitize message_id

    // Mark message as read for the user or admin (depending on message type)
    // Update chat_messages table for admin messages
    $update_admin_query = "
        UPDATE chat_messages 
        SET user_read = TRUE 
        WHERE message_id = ? AND user_id = ?";
    
    // Update contact_support table for user messages
    $update_user_query = "
        UPDATE contact_support 
        SET support_read = TRUE 
        WHERE support_id = ? AND user_id = ?";

    // Execute the appropriate update query
    if ($message_type === 'admin') {
        $stmt = $con->prepare($update_admin_query);
        $stmt->bind_param("ii", $message_id, $user_id);
    } else {
        $stmt = $con->prepare($update_user_query);
        $stmt->bind_param("ii", $message_id, $user_id);
    }

    if ($stmt->execute()) {
        $result->setErrorStatus(false);
        $result->setMessage("Message marked as read.");
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Error updating message read status.");
    }
    $stmt->close();
} else {
    $result->setErrorStatus(true);
    $result->setMessage("Message ID and User ID are required.");
}

// Prepare response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
