<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Fetch user_id from URL parameters
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    // Query to count the number of cart items for the given user_id
    $select_query = "
        SELECT COALESCE(SUM(quantity), 0) AS total_items
        FROM cart_details
        WHERE user_id = ?";

    $stmt = $con->prepare($select_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_query = $stmt->get_result();

    // Check if any result is found
    if ($result_query->num_rows > 0) {
        $row = $result_query->fetch_assoc();
        $total_items = $row['total_items'];

        // Return the total number of items in the cart
        $response['total_items'] = $total_items;
        $result->setErrorStatus(false);
        $result->setMessage("Cart item count fetched successfully.");
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No cart items found for this user.");
        $response['total_items'] = 0;
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
