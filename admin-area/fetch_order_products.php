<?php
require '../includes/connect.php';

$order_id = $_GET['order_id'];
$query = "
    SELECT oi.product_id, oi.quantity, oi.price, p.product_title, p.product_description 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
";

$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<table class='table table-bordered'>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['product_id']}</td>
                <td>{$row['product_title']}</td>
                <td>{$row['product_description']}</td>
                <td>{$row['quantity']}</td>
                <td>$" . number_format($row['price'], 2) . "</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No products found for this order.</p>";
}
?>
