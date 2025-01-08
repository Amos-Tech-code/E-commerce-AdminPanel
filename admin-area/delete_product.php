<?php
include('../includes/connect.php');
header('Content-Type: application/json');

if (isset($_GET['delete_product'])) {
    $delete_id = $_GET['delete_product'];

    // Delete query
    $delete_product = "DELETE FROM products WHERE product_id = $delete_id";
    $result_product = mysqli_query($con, $delete_product);

    if ($result_product) {
        // Return success response
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Product deletion failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
}
?>
