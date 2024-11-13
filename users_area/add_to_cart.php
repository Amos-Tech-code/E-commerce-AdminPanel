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
    // Ensure user_id and other necessary data are provided
    if (!empty($data->user_id) && !empty($data->product_id)) {
        $user_id = $data->user_id; // The user ID from the request
        $get_product_id = $data->product_id; // The product ID from the request
        
        // Check if the product is already in the cart
        $select_query = "SELECT * FROM cart_details WHERE user_id = ? AND product_id = ?";
        $stmt = $con->prepare($select_query);
        $stmt->bind_param("ii", $user_id, $get_product_id);
        $stmt->execute();
        $result_query = $stmt->get_result();
        $num_of_rows = mysqli_num_rows($result_query);
        
        if ($num_of_rows > 0) {
            $result->setErrorStatus(true);
            $result->setMessage("This item is already present in the cart");
        } else {
            // Add the item to the cart
            $insert_query = "INSERT INTO cart_details (product_id, user_id, quantity) VALUES (?, ?, 1)";
            $stmt = $con->prepare($insert_query);
            $stmt->bind_param("ii", $get_product_id, $user_id);
            
            if ($stmt->execute()) {
                $result->setErrorStatus(false);
                $result->setMessage("Item added to cart successfully");
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("Something went wrong. Please retry");
            }
        }
        $stmt->close();
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Insufficient parameters");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("No data received");
}

// Prepare response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
