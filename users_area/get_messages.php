<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Fetch user_id from URL parameters
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    // Fetch messages from both chat_messages (admin messages) and contact_support (user messages)
    $select_query = "
    SELECT 
    `message_id` AS id,
    `message`,
    `message_date` AS date,
    `user_read`,
    `admin_read`,
    'chat_message' AS message_type,
    `sent_by_user`
    FROM `chat_messages`
    WHERE `user_id` = ? OR `admin_id` = ? 

    UNION

    SELECT 
    `support_id` AS id,
    `message`,
    `message_date` AS date,
    `user_read`,
    `support_read` AS admin_read,
    'contact_support' AS message_type,
    `sent_by_user`
    FROM `contact_support`
    WHERE `user_id` = ?

    ORDER BY `date` ASC;";  // Sorting messages by date, ascending order

    // Prepare and execute the query
    $stmt = $con->prepare($select_query);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);  // Bind three parameters
    $stmt->execute();
    $result_query = $stmt->get_result();

    // Check if any messages are found
    if ($result_query->num_rows > 0) {
        $messages = array();
        while ($row = $result_query->fetch_assoc()) {
            $sender = ($row['sent_by_user'] == 1) ? 'user' : 'admin';  // Determine if the message is from the user or admin

            $messages[] = array(
                'id' => $row['id'],
                'message' => $row['message'],
                'date' => $row['date'],
                'message_type' => $row['message_type'],
                'user_read' => $row['user_read'],  // Indicates if the user has read the message
                'admin_read' => $row['admin_read'],  // Indicates if the admin/support has read the message
                'sender' => $sender  // 'user' or 'admin' based on the sent_by_user value
            );
        }
        $response['messages'] = $messages;
        $result->setErrorStatus(false);
        $result->setMessage("Messages fetched successfully.");
    } else {
        $result->setErrorStatus(false);
        $result->setMessage("No messages found for this user.");
        $response['messages'] = [];
    }

    $stmt->close();
} else {
    $result->setErrorStatus(true);
    $result->setMessage("User ID is required.");
}

// Prepare response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
