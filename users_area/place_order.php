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

        // Begin transaction
        $con->begin_transaction();

        try {
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
                $total_amount = 0;
                $order_items = [];

                while ($row = $cart_items->fetch_assoc()) {
                    $product_id = $row['product_id'];
                    $quantity = intval($row['quantity']);
                    $price = floatval($row['product_price']);
                    $subtotal = $quantity * $price;

                    $total_amount += $subtotal;

                    $order_items[] = [
                        'product_id' => $product_id,
                        'quantity' => $quantity,
                        'price' => $price
                    ];
                }

                // Fetch tax, shipping, and discount values from `ordercharges` table
                $charges_query = "SELECT tax, shipping, discount FROM ordercharges LIMIT 1";
                $charges_result = $con->query($charges_query);

                if ($charges_result && $charges_result->num_rows > 0) {
                    $charges_row = $charges_result->fetch_assoc();
                    $tax = floatval($charges_row['tax']);
                    $shipping = floatval($charges_row['shipping']);
                    $discount = floatval($charges_row['discount']);

                    $total_amount += $tax + $shipping - $discount;
                }

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

                // Insert order into `orders` table
                $insert_order_query = "
                    INSERT INTO orders (
                        userid, invoice_number, total_amount, order_status, 
                        first_name, last_name, mobile_no, email, address_line, 
                        postal_code, state, city, country
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $order_status = "Pending";

                $stmt_insert_order = $con->prepare($insert_order_query);
                $stmt_insert_order->bind_param(
                    "iisssssssssss", $user_id, $invoice_number, $total_amount, $order_status, 
                    $first_name, $last_name, $mobile_no, $email, $address_line, 
                    $postal_code, $state, $city, $country
                );                      

                if (!$stmt_insert_order->execute()) {
                    throw new Exception("Failed to place the order.");
                }

                $order_id = $stmt_insert_order->insert_id;

                // Insert items into `order_items` table
                $insert_items_query = "
                    INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)";
                $stmt_insert_items = $con->prepare($insert_items_query);

                foreach ($order_items as $item) {
                    $stmt_insert_items->bind_param(
                        "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']
                    );

                    if (!$stmt_insert_items->execute()) {
                        throw new Exception("Failed to insert order items.");
                    }
                }

                // Delete cart items for the user
                $delete_cart_query = "DELETE FROM cart_details WHERE user_id = ?";
                $stmt_delete = $con->prepare($delete_cart_query);
                $stmt_delete->bind_param("i", $user_id);

                if (!$stmt_delete->execute()) {
                    throw new Exception("Failed to clear the cart.");
                }

                // Commit the transaction
                $con->commit();

                $result->setErrorStatus(false);
                $result->setMessage("Order placed successfully and cart cleared.");
            } else {
                throw new Exception("No items found in the cart for the user.");
            }
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $con->rollback();

            $result->setErrorStatus(true);
            $result->setMessage($e->getMessage());
        } finally {
            $stmt->close();
        }
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
