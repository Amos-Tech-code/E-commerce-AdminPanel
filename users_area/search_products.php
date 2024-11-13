<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function search_products(){
    global $con;
    $response = array();
    $result = new Result();

    if(isset($_GET['search_data_product'])){
        $search_data_value = $_GET['search_data_product'];
        $search_query = "SELECT * FROM products WHERE Product_keyword LIKE '%$search_data_value%'";
        $result_query = mysqli_query($con, $search_query);
        $num_of_row = mysqli_num_rows($result_query);

        // If no results found
        if($num_of_row == 0){
            $result->setErrorStatus(true);
            $result->setMessage("Sorry, no results match.");
            $response['products'] = [];
        } else {
            $products = array();
            while($row = mysqli_fetch_assoc($result_query)){
                $products[] = array(
                    'product_id' => $row['product_id'],
                    'product_title' => $row['product_title'],
                    'product_description' => $row['product_description'],
                    'product_image' => './admin-area/product_images/' . $row['product_image1'],
                    'product_price' => $row['product_price']
                );
            }
            $result->setErrorStatus(false);
            $result->setMessage("Search results found.");
            $response['products'] = $products;
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Search query is missing.");
        $response['products'] = [];
    }

    // Set the response error and message
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    echo json_encode($response);
}
?>
