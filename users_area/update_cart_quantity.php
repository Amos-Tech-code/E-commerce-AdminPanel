<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

// Checking if the required data is present
if ($data != null) {
    // Ensure user_id, product_id, and quantity are provided
    if (!empty($data->user_id) && !empty($data->product_id) && isset($data->quantity)) {
        $user_id = intval($data->user_id); // The user ID from the request
        $product_id = intval($data->product_id); // The product ID from the request
        $quantity = intval($data->quantity); // The new quantity from the request

        // Ensure quantity is a valid number greater than zero
        if ($quantity > 0) {
            // Check if the product exists in the cart for the user
            $select_query = "SELECT * FROM cart_details WHERE product_id = ? AND user_id = ?";
            $stmt = $con->prepare($select_query);
            $stmt->bind_param("ii", $product_id, $user_id);
            $stmt->execute();
            $result_query = $stmt->get_result();

            if ($result_query->num_rows > 0) {
                // Update the quantity of the product in the cart
                $update_query = "UPDATE cart_details SET quantity = ? WHERE product_id = ? AND user_id = ?";
                $stmt = $con->prepare($update_query);
                $stmt->bind_param("iii", $quantity, $product_id, $user_id);

                if ($stmt->execute()) {
                    $result->setErrorStatus(false);
                    $result->setMessage("Cart quantity updated successfully for the product.");
                } else {
                    $result->setErrorStatus(true);
                    $result->setMessage("Failed to update cart quantity. Please retry.");
                }
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("No such product found in the cart for the provided user.");
            }
            $stmt->close();
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("Quantity must be greater than zero.");
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Insufficient parameters.");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("No data received.");
}

// Prepare response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
