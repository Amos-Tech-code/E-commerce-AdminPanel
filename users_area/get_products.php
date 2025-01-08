<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

function getproducts() {
    global $con;

    $response = array();
    $result = new Result();

    // Define limit for the number of products
    $limit = 20; // Adjust the limit as per your requirements

    // Prepare the query to select products with additional conditions and randomization
    $select_query = "
        SELECT product_id, product_title, product_description, product_image1, product_price, category_id, brand_id
        FROM products
        WHERE status = 1
          AND category_id NOT IN (1, 2, 3)
        ORDER BY RAND()
        LIMIT ?
    ";

    // Execute query and check if statement preparation is successful
    if ($stmt = $con->prepare($select_query)) {
        // Bind the limit as a parameter
        $stmt->bind_param("i", $limit);

        // Execute the statement
        $stmt->execute();
        $stmt->store_result();

        // Check if products are found
        if ($stmt->num_rows > 0) {

            $products = array();
            $stmt->bind_result($product_id, $product_title, $product_description, $product_image1, $product_price, $category_id, $brand_id);
            
            while ($stmt->fetch()) {
                $products[] = array(
                    'product_id' => $product_id,
                    'product_title' => $product_title,
                    'product_description' => $product_description,
                    'product_image' => './admin-area/product_images/' . $product_image1,
                    'product_price' => $product_price,
                    'category_id' => $category_id,
                    'brand_id' => $brand_id
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

        $stmt->close();
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Query preparation failed.");
        $response['products'] = array();
    }

    // Set final error and message in the response
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    // Output JSON response
    echo json_encode($response);
}

getproducts();
?>
