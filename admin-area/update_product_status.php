<?php
include('../includes/connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decode JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['product_id'], $input['status'])) {
        $productId = intval($input['product_id']);
        $newStatus = intval($input['status']);

        // Update the product status
        $sql = "UPDATE products SET status = ? WHERE product_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('ii', $newStatus, $productId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product status.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
