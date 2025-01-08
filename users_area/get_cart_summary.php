<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Check if user_id is passed as a GET parameter
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    // Fetch all cart items for the user and calculate the total amount
    $cart_query = "
        SELECT cd.cart_id, cd.product_id, cd.quantity, 
               p.product_title, p.product_image1, p.product_price 
        FROM cart_details cd 
        JOIN products p ON cd.product_id = p.product_id 
        WHERE cd.user_id = ?";
    $stmt = $con->prepare($cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();

    if ($cart_items->num_rows > 0) {
        // Calculate total amount from the cart
        $total_amount = 0;
        $items = [];

        while ($row = $cart_items->fetch_assoc()) {
            $cart_id = intval($row['cart_id']);
            $product_id = intval($row['product_id']);
            $quantity = intval($row['quantity']);
            $product_price = floatval($row['product_price']);
            $product_title = $row['product_title'];
            $product_image = $row['product_image1'];

            // Add the item's total to the total amount
            $total_amount += $quantity * $product_price;

            // Add item details to the items array
            $items[] = [
                "cart_id" => $cart_id,
                "product_id" => $product_id,
                "product_image" => $product_image,
                "product_name" => $product_title,
                "product_price" => number_format($product_price, 2),
                "quantity" => $quantity
            ];
        }

        // Fetch tax, shipping, and discount values from ordercharges table
        $charges_query = "SELECT tax, shipping, discount FROM ordercharges LIMIT 1";
        $charges_result = $con->query($charges_query);

        if ($charges_result && $charges_result->num_rows > 0) {
            $charges_row = $charges_result->fetch_assoc();
            $tax = floatval($charges_row['tax']);
            $shipping = floatval($charges_row['shipping']);
            $discount = floatval($charges_row['discount']);

            // Calculate the subtotal
            $subtotal = $total_amount + $tax + $shipping - $discount;

            // Prepare the response
            $response['error'] = false;
            $response['message'] = "Cart summary fetched successfully.";
            $response['data'] = [
                'amount' => number_format($total_amount, 2),
                'discount' => number_format($discount, 2),
                'shipping' => number_format($shipping, 2),
                'tax' => number_format($tax, 2),
                'subtotal' => number_format($subtotal, 2),
                'items' => $items
            ];
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("Failed to fetch order charges.");
            $response['error'] = $result->isError();
            $response['message'] = $result->getMessage();
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No items found in the cart for the user.");
        $response['error'] = $result->isError();
        $response['message'] = $result->getMessage();
    }

    $stmt->close();
} else {
    $result->setErrorStatus(true);
    $result->setMessage("User ID is required.");
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();
}

// Return the response
echo json_encode($response);
?>
