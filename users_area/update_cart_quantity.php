<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

if ($data != null) {
    // Ensure user_id, product_id, and quantity are provided
    if (!empty($data->user_id) && !empty($data->cart_id) && isset($data->quantity)) {
        $user_id = intval($data->user_id);
        $cart_id = intval($data->cart_id);
        $quantity = intval($data->quantity);

        if ($quantity > 0) {
            // Check if the product exists in the cart for the user
            $select_query = "SELECT * FROM cart_details WHERE cart_id = ? AND user_id = ?";
            $stmt = $con->prepare($select_query);
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
            $result_query = $stmt->get_result();

            if ($result_query->num_rows > 0) {
                // Update the quantity of the product in the cart
                $update_query = "UPDATE cart_details SET quantity = ? WHERE cart_id = ? AND user_id = ?";
                $stmt = $con->prepare($update_query);
                $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

                if ($stmt->execute()) {
                    // Fetch updated cart details
                    $cart_query = "
                        SELECT 
                            c.cart_id, 
                            c.product_id, 
                            c.quantity, 
                            p.product_price, 
                            p.product_image1, 
                            p.product_title 
                        FROM 
                            cart_details c 
                        JOIN 
                            products p 
                        ON 
                            c.product_id = p.product_id 
                        WHERE 
                            c.user_id = ?";
                    $cart_stmt = $con->prepare($cart_query);
                    $cart_stmt->bind_param("i", $user_id);
                    $cart_stmt->execute();
                    $cart_items_result = $cart_stmt->get_result();

                    if ($cart_items_result->num_rows > 0) {
                        $cart_items = [];
                        while ($row = $cart_items_result->fetch_assoc()) {
                            $cart_items[] = array(
                                'cart_id' => $row['cart_id'],
                                'product_id' => $row['product_id'],
                                'product_name' => $row['product_title'],
                                'product_price' => $row['product_price'],
                                'product_image' => './admin-area/product_images/' . $row['product_image1'],
                                'quantity' => $row['quantity']
                            );
                        }

                        $response['cart_items'] = $cart_items;
                        $result->setErrorStatus(false);
                        $result->setMessage("Cart quantity updated successfully.");
                    } else {
                        $result->setErrorStatus(true);
                        $result->setMessage("Failed to fetch updated cart items.");
                    }
                } else {
                    $result->setErrorStatus(true);
                    $result->setMessage("Failed to update cart quantity. Please try again.");
                }
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("No such cart for the provided user.");
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
if (isset($response['cart_items'])) {
    echo json_encode($response);
} else {
    echo json_encode(["error" => $response['error'], "message" => $response['message']]);
}
?>
