<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function view_details(){
    global $con;
    $response = array();
    $result = new Result();

    // Check if the product_id is set
    if(isset($_GET['product_id'])){
        $product_id = $_GET['product_id'];

        // Prevent using category or brand filters
        if(!isset($_GET['category']) && !isset($_GET['brand'])){
            $select_query = "SELECT * FROM products WHERE product_id = $product_id";
            $result_query = mysqli_query($con, $select_query);

            // If no product is found
            if(mysqli_num_rows($result_query) == 0){
                $result->setErrorStatus(true);
                $result->setMessage("Product not found.");
                $response['product'] = null;
            } else {
                // Fetch product details
                $product_details = array();
                while($row = mysqli_fetch_assoc($result_query)){
                    $product_details = array(
                        'product_id' => $row['product_id'],
                        'product_title' => $row['product_title'],
                        'product_description' => $row['product_description'],
                        'product_price' => $row['product_price'],
                        'product_image1' => './admin-area/product_images/' . $row['product_image1'],
                        'product_image2' => './admin-area/product_images/' . $row['product_image2'],
                        'product_image3' => './admin-area/product_images/' . $row['product_image3'],
                        'category_id' => $row['category_id'],
                        'brand_id' => $row['brand_id']
                    );
                }

                $result->setErrorStatus(false);
                $result->setMessage("Product details found.");
                $response['product'] = $product_details;

                // You can also query related products if needed (optional)
                // $related_query = "SELECT * FROM products WHERE category_id = {$product_details['category_id']} LIMIT 4";
                // Fetch and add related products to the response
                // Example:
                // $response['related_products'] = $related_products;
            }
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Product ID is missing.");
        $response['product'] = null;
    }

    // Set the response error and message
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    // Send the JSON response
    echo json_encode($response);
}
?>
