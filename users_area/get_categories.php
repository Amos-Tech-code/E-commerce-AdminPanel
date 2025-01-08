<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

function getcategories() {
    global $con;
    $response = array();
    $result = new Result();

    // Query to fetch categories
    $select_categories = "SELECT * FROM categories";
    $result_categories = mysqli_query($con, $select_categories);

    // Check if categories are found
    if (mysqli_num_rows($result_categories) > 0) {
        $categories = array();

        // Fetch all categories
        while ($row_data = mysqli_fetch_assoc($result_categories)) {
            $categories[] = array(
                'category_id' => $row_data['category_id'],
                'category_title' => $row_data['category_title']
            );
        }

        $result->setErrorStatus(false);
        $result->setMessage("Categories retrieved successfully.");
        $response['categories'] = $categories;
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No categories found.");
        $response['categories'] = array();
    }

    // Set the response error and message
    $response['error'] = $result->isError();
    $response['message'] = $result->getMessage();

    // Send the JSON response
    echo json_encode($response);
}

// Call the function to output the response
getcategories();
?>
