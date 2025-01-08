<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Fetch user_id from URL parameters
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id

    // Query to get order details along with customer details
    $select_query = "
        SELECT 
            o.order_id, 
            o.invoice_number,
            o.total_amount AS amount, 
            o.order_status,
            o.first_name,
            o.last_name,
            o.mobile_no,
            o.email,
            o.address_line,
            o.postal_code,
            o.state,
            o.city,
            o.country,
            o.order_date
        FROM 
            orders o
        WHERE 
            o.userid = ?";

    $stmt = $con->prepare($select_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_query = $stmt->get_result();

    // Check if any orders are found
    if ($result_query->num_rows > 0) {
        $order_details = array();

        while ($row = $result_query->fetch_assoc()) {
            // Prepare order data
            $order_id = $row['order_id'];
            $order_data = array(
                'order_id' => $order_id,
                'invoice_number' => $row['invoice_number'],
                'amount' => $row['amount'],
                'order_status' => $row['order_status'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'mobile_no' => $row['mobile_no'],
                'email' => $row['email'],
                'address_line' => $row['address_line'],
                'postal_code' => $row['postal_code'],
                'state' => $row['state'],
                'city' => $row['city'],
                'country' => $row['country'],
                'order_date' => $row['order_date'],
                'items' => []
            );

            // Fetch order items for the order
            $items_query = "
                SELECT 
                    oi.product_id, 
                    p.product_title,
                    oi.quantity, 
                    oi.price
                FROM 
                    order_items oi
                JOIN 
                    products p ON oi.product_id = p.product_id
                WHERE 
                    oi.order_id = ?";
            
            $stmt_items = $con->prepare($items_query);
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $items_result = $stmt_items->get_result();

            // Check if order items are found
            if ($items_result->num_rows > 0) {
                while ($item_row = $items_result->fetch_assoc()) {
                    $order_data['items'][] = array(
                        'product_id' => $item_row['product_id'],
                        'product_title' => $item_row['product_title'],
                        'quantity' => $item_row['quantity'],
                        'price' => $item_row['price']
                    );
                }
            }

            // Add the order data to the response array
            $order_details[] = $order_data;
        }

        // Prepare success response
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
