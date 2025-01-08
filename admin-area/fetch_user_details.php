<?php
require '../includes/connect.php';

$order_id = $_GET['order_id'];
$query = "
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(oi.product_id, ' (Qty: ', oi.quantity, ')') SEPARATOR ', ') AS product_details
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id = $order_id
    GROUP BY o.order_id
";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    echo "
        <p><strong>Name:</strong> {$row['first_name']} {$row['last_name']}</p>
        <p><strong>Email:</strong> {$row['email']}</p>
        <p><strong>Mobile No:</strong> {$row['mobile_no']}</p>
        <p><strong>Address:</strong> {$row['address_line']}, {$row['city']}, {$row['state']}, {$row['postal_code']}</p>
        <p><strong>Country:</strong> {$row['country']}</p>
        <p><strong>Product Details:</strong> {$row['product_details']}</p>
        <p><strong>Order Status:</strong> {$row['order_status']}</p>
    ";
} else {
    echo "<p>User details not found.</p>";
}
?>
