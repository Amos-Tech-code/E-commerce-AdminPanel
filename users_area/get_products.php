<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function getproducts(){
    global $con;
    $response = array();
    $result = new Result();

    // Query to fetch products
    $select_query = "SELECT * FROM products";
    $result_query = mysqli_query($con, $select_query);

    // Check if products are found
    if(mysqli_num_rows($result_query) > 0){
        $products = array();

        // Fetch all products
        while($row = mysqli_fetch_assoc($result_query)){
            $products[] = array(
                'product_id' => $row['product_id'],
                'product_title' => $row['product_title'],
                'product_description' => $row['product_description'],
                'product_image' => './admin-area/product_images/'.$row['product_image1'],
                'product_price' => $row['product_price'],
                'category_id' => $row['category_id'],
                'brand_id' => $row['brand_id']
            );
        }

        $result->setErrorStatus(false);
        $result->setMessage("Products retrieved successfully.");
        $response['products'] = $products;
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No products found.");
        $response['products'] = array();
    }

    // Set the response error and message
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    // Send the JSON response
    echo json_encode($response);
}
?>
