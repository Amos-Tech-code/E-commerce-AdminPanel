<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");
function get_unique_categories() {
    global $con;
    $response = array();
    $result = new Result();

    if (isset($_GET['category'])) {
        $category_id = $_GET['category'];
        $select_query = "SELECT * FROM products WHERE category_id=$category_id";
        $result_query = mysqli_query($con, $select_query);
        $num_of_row = mysqli_num_rows($result_query);

        if ($num_of_row == 0) {
            $result->setErrorStatus(true);
            $result->setMessage("No stock for this category.");
            $response['products'] = [];
        } else {
            $products = array();
            while ($row = mysqli_fetch_assoc($result_query)) {
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
