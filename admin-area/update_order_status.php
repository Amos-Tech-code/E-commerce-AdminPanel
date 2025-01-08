<?php
require '../includes/connect.php';

if (isset($_GET['order_id']) && isset($_GET['status'])) {
    $order_id = (int) $_GET['order_id'];
    $status = $_GET['status'];

    // Update order status
    $query = "UPDATE orders SET order_status = '$status' WHERE order_id = $order_id";
    if (mysqli_query($con, $query)) {
        echo "Order status updated to " . ucfirst($status) . ".";
    } else {
        echo "Failed to update order status.";
    }
} else {
    echo "Invalid request.";
}
?>
