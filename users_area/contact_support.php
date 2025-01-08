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

    // Insert message into contact_support table
    $insert_query = "
        INSERT INTO contact_support (user_id, message, user_read, support_read, sent_by_user)
        VALUES (?, ?, TRUE, FALSE, 1)";  // Default: user has read it, support hasn't read it

    $stmt = $con->prepare($insert_query);
    $stmt->bind_param("is", $user_id, $message);

    // Execute the query
    if ($stmt->execute()) {
        $response['error'] = false;
        $response['message'] = "Message sent to support successfully.";
    } else {
        $response['error'] = true;
        $response['message'] = "Failed to send message to support.";
    }

    $stmt->close();
} else {
    $response['error'] = true;
    $response['message'] = "User ID and message are required.";
}

// Output response as JSON
echo json_encode($response);
?>
