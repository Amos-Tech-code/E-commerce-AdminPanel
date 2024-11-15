<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

function getproducts() {
    global $con;

    $response = array();
    $result = new Result();

    // Test the database connection
    /*if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    } else {
        echo "Database connection successful.\n";  // Debugging log
    }*/

    // Prepare the query to select products
    $select_query = "SELECT product_id, product_title, product_description, product_image1, product_price, category_id, brand_id FROM products";
    //echo "Preparing query: $select_query\n";  // Debugging log

    // Execute query and check if statement preparation is successful
    if ($stmt = $con->prepare($select_query)) {
       // echo "Query preparation successful.\n";  // Debugging log

        // Execute the statement
        $stmt->execute();
        $stmt->store_result();

        // Check if products are found
        if ($stmt->num_rows > 0) {
            //echo "Number of products found: " . $stmt->num_rows . "\n";  // Debugging log

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
            echo "No products found.\n";  // Debugging log
            $result->setErrorStatus(true);
            $result->setMessage("No products found.");
            $response['products'] = array();
        }

        $stmt->close();
    } else {
        //echo "Failed to prepare query: " . $con->error . "\n";  // Detailed error log
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
