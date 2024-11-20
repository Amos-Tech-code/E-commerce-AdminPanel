<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

$response = array();
$result = new Result();

// Takes raw data from the request
$json = file_get_contents('php://input');
$data = json_decode($json);

if ($data != null) {
    if (!empty($data->user_id)) {
        $user_id = intval($data->user_id); // Sanitize user_id

        // Validate all required fields
        $required_fields = [
            'first_name', 'last_name', 'mobile_no', 'email',
            'address_line', 'postal_code', 'state', 'city', 'country'
        ];
        foreach ($required_fields as $field) {
            if (empty($data->$field)) {
                $result->setErrorStatus(true);
                $result->setMessage("$field is required.");
                echo json_encode([
                    'error' => $result->isError(),
                    'message' => $result->getMessage()
                ]);
                exit;
            }
        }

        // Extract customer details
        $first_name = $data->first_name;
        $last_name = $data->last_name;
        $mobile_no = $data->mobile_no;
        $email = $data->email;
        $address_line = $data->address_line;
        $postal_code = $data->postal_code;
        $state = $data->state;
        $city = $data->city;
        $country = $data->country;

        // Fetch all cart items for the user and calculate the total amount
        $cart_query = "
            SELECT cd.product_id, cd.quantity, p.product_price 
            FROM cart_details cd 
            JOIN products p ON cd.product_id = p.product_id 
            WHERE cd.user_id = ?";
        $stmt = $con->prepare($cart_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_items = $stmt->get_result();

        if ($cart_items->num_rows > 0) {
            // Initialize arrays to store product IDs and quantities
            $product_ids = [];
            $quantities = [];
            $total_amount = 0;

            while ($row = $cart_items->fetch_assoc()) {
                $product_id = $row['product_id'];
                $quantity = intval($row['quantity']);
                $price = floatval($row['product_price']);

                $product_ids[] = $product_id;
                $quantities[] = $quantity;

                // Calculate total amount
                $total_amount += $quantity * $price;
            }

            // Combine product_ids and quantities as JSON strings
            $product_ids_json = json_encode($product_ids);
            $quantities_json = json_encode($quantities);

            // Generate a unique invoice number
            do {
                $invoice_number = mt_rand(100000, 999999);
                $check_invoice_query = "SELECT COUNT(*) AS count FROM orders WHERE invoice_number = ?";
                $stmt_invoice = $con->prepare($check_invoice_query);
                $stmt_invoice->bind_param("i", $invoice_number);
                $stmt_invoice->execute();
                $invoice_result = $stmt_invoice->get_result();
                $row_invoice = $invoice_result->fetch_assoc();
                $is_unique = $row_invoice['count'] == 0;
                $stmt_invoice->close();
            } while (!$is_unique);

            // Insert combined order into orders table
            $insert_query = "
                INSERT INTO orders (
                    userid, invoice_number, product_id, quantity, amount, order_status, 
                    first_name, last_name, mobile_no, email, address_line, 
                    postal_code, state, city, country
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $order_status = "Pending";

            $stmt_insert = $con->prepare($insert_query);
            $stmt_insert->bind_param(
                "iissdssssssssss",
                $user_id, $invoice_number, $product_ids_json, $quantities_json, $total_amount, $order_status, 
                $first_name, $last_name, $mobile_no, $email, $address_line, 
                $postal_code, $state, $city, $country
            );

            if ($stmt_insert->execute()) {
                // Delete cart items for the user
                $delete_cart_query = "DELETE FROM cart_details WHERE user_id = ?";
                $stmt_delete = $con->prepare($delete_cart_query);
                $stmt_delete->bind_param("i", $user_id);

                if ($stmt_delete->execute()) {
                    $result->setErrorStatus(false);
                    $result->setMessage("Order placed successfully and cart cleared.");
                } else {
                    $result->setErrorStatus(true);
                    $result->setMessage("Failed to clear the cart.");
                }
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("Failed to place the order.");
            }
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("No items found in the cart for the user.");
        }

        $stmt->close();
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("User ID is required.");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("Invalid or empty data received.");
}

// Prepare response
$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
