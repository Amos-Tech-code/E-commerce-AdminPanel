<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Check if cart_id and user_id are provided in the request
if (isset($_GET['cart_id']) && isset($_GET['user_id'])) {
    $cart_id = intval($_GET['cart_id']); // Sanitize cart_id
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    if ($cart_id > 0 && $user_id > 0) {
        // Check if the cart item exists
        $check_query = "SELECT * FROM cart_details WHERE cart_id = ? AND user_id = ?";
        $stmt_check = $con->prepare($check_query);
        $stmt_check->bind_param("ii", $cart_id, $user_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            // Delete the cart item
            $delete_query = "DELETE FROM cart_details WHERE cart_id = ? AND user_id = ?";
            $stmt_delete = $con->prepare($delete_query);
            $stmt_delete->bind_param("ii", $cart_id, $user_id);

            if ($stmt_delete->execute()) {
                $result->setErrorStatus(false);
                $result->setMessage("Cart item deleted successfully.");
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("Failed to delete the cart item. Please try again.");
            }
            $stmt_delete->close();
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("No such cart item found for the provided user.");
        }
        $stmt_check->close();
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Invalid cart_id or user_id.");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("Both cart_id and user_id are required.");
}

// Prepare and send the response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
