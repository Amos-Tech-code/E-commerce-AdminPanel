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
                // Fetch updated cart items
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
                            'product_price' => number_format($row['product_price'], 2),
                            'product_image' => './admin-area/product_images/' . $row['product_image1'],
                            'quantity' => $row['quantity']
                        );
                    }
                    $response['cart_items'] = $cart_items;
                    $result->setErrorStatus(false);
                    $result->setMessage("Cart item deleted successfully.");
                } else {
                    // Cart is now empty
                    $response['cart_items'] = [];
                    $result->setErrorStatus(false);
                    $result->setMessage("Cart item deleted successfully. No items left in the cart.");
                }
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

// Prepare response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
