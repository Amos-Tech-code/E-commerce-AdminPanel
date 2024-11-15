<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");


function search_products(){
    global $con;
    $response = array();
    $result = new Result();

    if(isset($_GET['search_data_product'])){
        // Get the search term and split it into individual keywords
        $search_data_value = mysqli_real_escape_string($con, $_GET['search_data_product']);
        $keywords = explode(' ', $search_data_value); // Split the search term by spaces

        // Create a dynamic query using LIKE clauses for each keyword across multiple fields
        $search_query = "SELECT * FROM products WHERE";
        $search_conditions = array();

        // Adding conditions for each keyword to search across multiple fields (title, description, keywords)
        foreach ($keywords as $keyword) {
            $search_conditions[] = " product_title LIKE '%$keyword%' 
                                    OR product_description LIKE '%$keyword%' 
                                    OR Product_keyword LIKE '%$keyword%'";
        }

        // Combine all conditions with OR operators
        $search_query .= implode(' OR ', $search_conditions);

        // Execute the query
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
        search_products();
?>
