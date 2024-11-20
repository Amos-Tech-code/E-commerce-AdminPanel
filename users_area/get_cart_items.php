<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Fetch user_id from URL parameters
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    // Query to get cart items with product details
    $select_query = "
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
    
    $stmt = $con->prepare($select_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_query = $stmt->get_result();

    // Check if any items are found
    if ($result_query->num_rows > 0) {
        $cart_items = array();
        while ($row = $result_query->fetch_assoc()) {
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
        $result->setMessage("Cart items fetched successfully.");
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No items found in the cart.");
        $response['cart_items'] = [];
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
