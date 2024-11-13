<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function cart_item(){
    global $con;
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    $response = array();
    $result = new Result();

    if ($data != null) {
        if (!empty($data->user_id)) {
            $user_id = $data->user_id;

            // Query to fetch cart details along with product details
            $select_query = "
                SELECT p.product_id, p.product_title, p.product_description, p.product_image1, p.product_price, c.quantity
                FROM cart_details c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = ?
            ";

            // Prepare and execute the statement
            $stmt = $con->prepare($select_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result_query = $stmt->get_result();

            // Initialize an array to store the cart items
            $cart_items = array();

            // Fetch all cart items with product details
            while ($row = $result_query->fetch_assoc()) {
                $cart_items[] = array(
                    'product_id' => $row['product_id'],
                    'product_title' => $row['product_title'],
                    'product_description' => $row['product_description'],
                    'product_image' => $row['product_image1'],
                    'product_price' => $row['product_price'],
                    'quantity' => $row['quantity'],
                    'total_price' => $row['product_price'] * $row['quantity']  // Calculate total price for this item
                );
            }

            // Check if there are any items in the cart
            if (count($cart_items) > 0) {
                $response['error'] = false;
                $response['message'] = "Cart items retrieved successfully";
                $response['cart_items'] = $cart_items;
            } else {
                $response['error'] = false;
                $response['message'] = "No items in cart";
                $response['cart_items'] = array();  // Return an empty array if no items found
            }

            $stmt->close();
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("User ID is required");
            $response['error'] = true;
            $response['message'] = $result->getMessage();
        }
    } else {
        $response['error'] = true;
        $response['message'] = "No data received";
    }

    // Return the response as JSON
    echo json_encode($response);
}
?>