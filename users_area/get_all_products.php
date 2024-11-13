<?php

require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function get_all_products() {
    global $con;
    $response = array();
    $result = new Result();

    // Check if category or brand is not set
    if (!isset($_GET['category']) && !isset($_GET['brand'])) {
        $select_query = "SELECT * FROM products LIMIT 0,9";
        $result_query = mysqli_query($con, $select_query);

        // Check if query is successful
        if ($result_query) {
            $products = array();
            while ($row = mysqli_fetch_assoc($result_query)) {
                $products[] = array(
                    'product_id' => $row['product_id'],
                    'product_title' => $row['product_title'],
                    'product_description' => $row['product_description'],
                    'product_image' => './admin-area/product_images/' . $row['product_image1'],
                    'product_price' => $row['product_price']
                );
            }
            $result->setErrorStatus(false);
            $result->setMessage("Products fetched successfully.");
            $response['products'] = $products;
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("Failed to fetch products.");
        }
    }

    // Set the response error and message
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    echo json_encode($response);
}
?>
