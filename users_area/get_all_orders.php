<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Fetch user_id from URL parameters
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    // Query to get order details
    $select_query = "
        SELECT 
            order_id, 
            order_date, 
            amount, 
            order_status 
        FROM 
            orders 
        WHERE 
            userid = ?";
    
    $stmt = $con->prepare($select_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_query = $stmt->get_result();

    // Check if any orders are found
    if ($result_query->num_rows > 0) {
        $order_details = array();
        while ($row = $result_query->fetch_assoc()) {
            $order_details[] = array(
                'order_id' => $row['order_id'],
                'order_date' => $row['order_date'],
                'amount' => $row['amount'],
                'order_status' => $row['order_status']
            );
        }
        $response['order_details'] = $order_details;
        $result->setErrorStatus(false);
        $result->setMessage("Order details fetched successfully.");
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No orders found for this user.");
        $response['order_details'] = [];
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
