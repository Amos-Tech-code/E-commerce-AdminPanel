<?php
// Fetch data for dashboard statistics
$product_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM products");
$product_count = $product_result ? mysqli_fetch_array($product_result)['total'] : 0;

$user_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM user_table");
$user_count = $user_result ? mysqli_fetch_array($user_result)['total'] : 0;

$order_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM orders WHERE order_status = 'Pending'");
$order_count = $order_result ? mysqli_fetch_array($order_result)['total'] : 0;

$brand_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM brands");
$brand_count = $brand_result ? mysqli_fetch_array($brand_result)['total'] : 0;

$category_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM categories");
$category_count = $category_result ? mysqli_fetch_array($category_result)['total'] : 0;
$recent_orders = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";

//Fetch data for top product
$topProductQuery = "SELECT p.product_title, SUM(oi.quantity) AS total_sold 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.product_id 
          GROUP BY oi.product_id 
          ORDER BY total_sold DESC 
          LIMIT 1";
$result = mysqli_query($con, $topProductQuery);
$row = mysqli_fetch_assoc($result);
$top_product = $row['product_title'] ?? 'N/A'; // Default to 'N/A' if no data

//Fetch data for Monthly Revenue
$monthlyReveueQuery = "SELECT SUM(total_amount) AS monthly_revenue 
          FROM orders 
          WHERE order_status = 'Delivered'
          AND MONTH(order_date) = MONTH(CURRENT_DATE()) 
          AND YEAR(order_date) = YEAR(CURRENT_DATE())";
$result = mysqli_query($con, $monthlyReveueQuery);
$row = mysqli_fetch_assoc($result);
$monthly_revenue = $row['monthly_revenue'] ?? 0; // Default to 0 if no data

//Fetch data for Total Revenue
$totalRevenueQuery = "SELECT SUM(total_amount) AS total_revenue 
          FROM orders 
          WHERE order_status = 'Delivered'";
$result = mysqli_query($con, $totalRevenueQuery);
$row = mysqli_fetch_assoc($result);
$total_revenue = $row['total_revenue'] ?? 0; // Default to 0 if no data

// Fetch monthly order data for the chart
// Calculate the last six months
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $months[] = date("M Y", strtotime("-$i month")); // Format: "Jan 2025"
}
$month_labels = json_encode($months);

// Fetch order data for the last six months
$order_data_query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS order_count
                     FROM orders
                     WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                     GROUP BY month
                     ORDER BY month";
$order_data_result = mysqli_query($con, $order_data_query);

// Initialize order and sales data arrays
$order_data = array_fill(0, 6, 0); // Default to 0 for six months
$sales_data = array_fill(0, 6, 0); // Default to 0 for six months

while ($row = mysqli_fetch_assoc($order_data_result)) {
    $month_index = array_search(date("M Y", strtotime($row['month'] . "-01")), $months);
    if ($month_index !== false) {
        $order_data[$month_index] = (int)$row['order_count'];
    }
}

// Fetch sales data for delivered orders
$sales_data_query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, SUM(total_amount) AS total_sales
                     FROM orders
                     WHERE order_status = 'delivered'
                     AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                     GROUP BY month
                     ORDER BY month";
$sales_data_result = mysqli_query($con, $sales_data_query);

while ($row = mysqli_fetch_assoc($sales_data_result)) {
    $month_index = array_search(date("M Y", strtotime($row['month'] . "-01")), $months);
    if ($month_index !== false) {
        $sales_data[$month_index] = (float)$row['total_sales'];
    }
}


//Query for Revenue Distribution by Category
$revenueByCategoryQuery = "
    SELECT c.category_title, SUM(oi.quantity * p.product_price) AS revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    JOIN categories c ON p.category_id = c.category_id
    WHERE oi.order_id IN (
        SELECT order_id FROM orders WHERE order_status = 'Delivered'
    )
    GROUP BY c.category_id
    ORDER BY revenue DESC
    LIMIT 10";
$result = mysqli_query($con, $revenueByCategoryQuery);

$categories = [];
$categoryRevenue = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['category_title'];
    $categoryRevenue[] = $row['revenue'];
}

//Query for Revenue Distribution by Brand
$revenueByBrandQuery = "
    SELECT b.brand_title, SUM(oi.quantity * p.product_price) AS revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    JOIN brands b ON p.brand_id = b.brand_id
    WHERE oi.order_id IN (
        SELECT order_id FROM orders WHERE order_status = 'Delivered'
    )
    GROUP BY b.brand_id
    ORDER BY revenue DESC
    LIMIT 10";
$result = mysqli_query($con, $revenueByBrandQuery);

$brands = [];
$brandRevenue = [];

while ($row = mysqli_fetch_assoc($result)) {
    $brands[] = $row['brand_title'];
    $brandRevenue[] = $row['revenue'];
}


// Fetch order distribution by status
$order_status_query = "SELECT order_status, COUNT(*) AS count
                       FROM orders
                       GROUP BY order_status";
$order_status_result = mysqli_query($con, $order_status_query);

$order_statuses = [];
$order_counts = [];

while ($row = mysqli_fetch_assoc($order_status_result)) {
    $order_statuses[] = ucfirst($row['order_status']); // Capitalize status for display
    $order_counts[] = (int)$row['count'];
}

// Encode data for JavaScript
$order_status_labels = json_encode($order_statuses);
$order_status_data = json_encode($order_counts);


// Fetch customer segmentation by spending
$customer_segmentation_query = "
    SELECT 
        CASE 
            WHEN total_spent < 100 THEN 'Low Spenders'
            WHEN total_spent BETWEEN 100 AND 1000 THEN 'Medium Spenders'
            ELSE 'High Spenders'
        END AS spending_category,
        COUNT(*) AS customer_count
    FROM (
        SELECT userid, SUM(total_amount) AS total_spent
        FROM orders
        WHERE order_status = 'Delivered'
        GROUP BY userid
    ) AS spending
    GROUP BY spending_category
    ORDER BY spending_category";
    
$customer_segmentation_result = mysqli_query($con, $customer_segmentation_query);

$segmentation_labels = [];
$segmentation_data = [];

while ($row = mysqli_fetch_assoc($customer_segmentation_result)) {
    $segmentation_labels[] = $row['spending_category'];
    $segmentation_data[] = (int)$row['customer_count'];
}

// Encode data for JavaScript
$customer_segmentation_labels = json_encode($segmentation_labels);
$customer_segmentation_counts = json_encode($segmentation_data);


?>