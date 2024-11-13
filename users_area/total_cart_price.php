<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function total_cart_price(){
    global $con;
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    $response = array();
    $result = new Result();

    if ($data != null) {
        if (!empty($data->user_id)) {
            $user_id = $data->user_id;
            $total = 0;

            $cart_query = "SELECT * FROM cart_details WHERE user_id = ?";
            $stmt = $con->prepare($cart_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = mysqli_fetch_array($result)) {
                $product_id = $row['product_id'];
                $select_products = "SELECT product_price FROM products WHERE product_id = ?";
                $stmt2 = $con->prepare($select_products);
                $stmt2->bind_param("i", $product_id);
                $stmt2->execute();
                $result_products = $stmt2->get_result();

                while ($row_product_price = mysqli_fetch_array($result_products)) {
                    $product_price = $row_product_price['product_price'];
                    $total += $product_price;
                }
                $stmt2->close();
            }

            $response['error'] = false;
            $response['message'] = $total;
        } else {
            $response['error'] = true;
            $response['message'] = "User ID is required";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "No data received";
    }

    echo json_encode($response);
}
?>