<?php
session_start();
include('../includes/connect.php');
include('../functions/common_functions.php');

if(isset($_SESSION['username'])){
    $UserName = $_SESSION['username'];
    // Validate user ID existence
    $validate_user_query = "SELECT user_id FROM user_table WHERE username = '$UserName'";
    $result_validate_user = mysqli_query($con, $validate_user_query);

    if(mysqli_num_rows($result_validate_user) == 0) {
        // User ID doesn't exist in the users table
        echo "Invalid user ID.";
        exit; // or handle the error accordingly
    }

    $userRow = mysqli_fetch_assoc($result_validate_user);
    $UserId = $userRow['user_id'];

    // Proceed with inserting the order into user_orders table

    // getting total items and total price of all items
    $get_ip_address = getIPAddress();
    $total_price = 0;
    $cart_query_price = "SELECT * FROM cart_details WHERE ip_address='$get_ip_address'";
    $result_cart_price = mysqli_query($con, $cart_query_price);
    $invoice_number = mt_rand();
    $status = 'pending';
    $count_products = mysqli_num_rows($result_cart_price);
    while($row_price = mysqli_fetch_array($result_cart_price)){
        $product_id = $row_price['product_id'];
        $select_product = "SELECT * FROM products WHERE product_id=$product_id";
        $run_price = mysqli_query($con, $select_product);
        while($row_product_price = mysqli_fetch_array($run_price)){
            $product_price = array($row_product_price['product_price']);
            $product_values = array_sum($product_price);
            $total_price += $product_values;
        }
    }

    //getting quantity from cart
    $get_cart = "SELECT * FROM cart_details";
    $run_cart = mysqli_query($con, $get_cart);
    $get_item_quantity = mysqli_fetch_array($run_cart);
    $quantity = $get_item_quantity['quantity'];
    if($quantity == 0){
        $quantity = 1;
        $subtotal = $total_price;
    } else {
        $quantity = $quantity;
        $subtotal = $total_price * $quantity;
    }

    $insert_orders = "INSERT INTO user_orders (user_id, amount_due, invoice_number, total_products, order_date, order_status)
                      VALUES ('$UserId', '$subtotal', '$invoice_number', '$count_products', NOW(), '$status')";
    $result_query = mysqli_query($con, $insert_orders);

    if($result_query){
        echo "<script>alert('Orders submitted successfully')</script>";
        echo "<script>window.open('profile.php','_self')</script>";
    }

    // orders pending
    $insert_pending_orders = "INSERT INTO orders_pending (userid, invoice_number, product_id, quantity, order_status)
                              VALUES ('$UserId', '$invoice_number', '$product_id', '$quantity', '$status')";
    $result_pending_orders = mysqli_query($con, $insert_pending_orders);

    // delete
    $empty_cart = "DELETE FROM cart_details WHERE ip_address='$get_ip_address'";
    $result_delete = mysqli_query($con, $empty_cart);

    // Add the hidden input field for user ID
    echo "<input type='hidden' name='user_id' value='$UserId'>";
}

?>
