<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Get raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if the necessary fields are present
if (isset($data['user_id']) && !empty($data['user_id']) && isset($data['message']) && !empty($data['message'])) {
    $user_id = intval($data['user_id']); // Sanitize user_id
    $message = mysqli_real_escape_string($con, $data['message']); // Sanitize message content

    // Check if any admin is available, if not set admin_id to NULL
    $admin_id_query = "SELECT admin_id FROM admin_table LIMIT 1";  // Check for any available admin (select the first admin for simplicity)
    $admin_result = $con->query($admin_id_query);
    if ($admin_result->num_rows > 0) {
        $admin_row = $admin_result->fetch_assoc();
        $admin_id = $admin_row['admin_id'];
    } else {
        $admin_id = NULL;  // If no admin available, set admin_id to NULL
    }

    // Insert message into chat_messages table (sent by user)
    $insert_query = "
        INSERT INTO chat_messages (user_id, admin_id, message, sent_by_user, user_read, admin_read)
        VALUES (?, ?, ?, 1, 0, 0)";  // sent_by_user = 1 for user message, unread by both user and admin initially

    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("iis", $user_id, $admin_id, $message);

    // Execute the query
    if ($stmt->execute()) {
        $response['error'] = false;
        $response['message'] = "Message sent to admin successfully.";
    } else {
        $response['error'] = true;
        $response['message'] = "Failed to send message to admin.";
    }

    $stmt->close();
} else {
    $response['error'] = true;
    $response['message'] = "User ID and message are required.";
}

// Output response as JSON
echo json_encode($response);
?>
