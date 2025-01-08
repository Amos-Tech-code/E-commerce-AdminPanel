<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

function get_unique_categories() {
    global $con;
    $response = array();
    $result = new Result();
    
    // Add Content-Type header
    header('Content-Type: application/json; charset=utf-8');

    if (isset($_GET['category'])) {
        $category_id = intval($_GET['category']);
        $limit = 20; // Adjust the limit as needed
        
        // Prepare the query
        $select_query = "SELECT product_id, product_title, product_description, product_image1, product_price, category_id, brand_id 
                         FROM products 
                         WHERE category_id = ? AND status = 1 
                         ORDER BY RAND() 
                         LIMIT ?";
        
        if ($stmt = $con->prepare($select_query)) {
            $stmt->bind_param("ii", $category_id, $limit); // Bind the category_id and limit
            
            $stmt->execute();
            $result_query = $stmt->get_result();
            
            $num_of_row = $result_query->num_rows;
            
            if ($num_of_row == 0) {
                $result->setErrorStatus(true);
                $result->setMessage("No stock for this category.");
                $response['products'] = [];
            } else {
                $products = array();
                while ($row = $result_query->fetch_assoc()) {
                    $products[] = array(
                        'product_id' => $row['product_id'],
                        'product_title' => $row['product_title'],
                        'product_description' => $row['product_description'],
                        'product_image' => './admin-area/product_images/' . $row['product_image1'],
                        'product_price' => $row['product_price'],
                        'category_id' => $row['category_id'],
                        'brand_id' => $row['brand_id'],
                    );
                }
                $result->setErrorStatus(false);
                $result->setMessage("Products for category fetched successfully.");
                $response['products'] = $products;
            }
            
            $stmt->close();
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("Query preparation failed.");
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Category ID is required.");
    }

    // Set the response error and message
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    echo json_encode($response);
}

get_unique_categories();
?>
