<?php
include('../includes/connect.php');

// Handle product status updates (put on hold or activate)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productId = $_POST['product_id'];
    if (isset($_POST['put_on_hold'])) {
        $sql = "UPDATE products SET status = 0 WHERE product_id = $productId";
        if ($con->query($sql) !== TRUE) {
            echo "<p class='text-bg-danger'>Error putting product on hold: " . $con->error . "</p>";
        }
    } elseif (isset($_POST['activate'])) {
        $sql = "UPDATE products SET status = 1 WHERE product_id = $productId";
        if ($con->query($sql) !== TRUE) {
            echo "<p class='text-bg-danger'>Error activating product: " . $con->error . "</p>";
        }
    }
}

// Get category options for filtering
/*
$category_query = "SELECT DISTINCT category FROM products";
$category_result = mysqli_query($con, $category_query);

// Filtering criteria
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$order_by = isset($_GET['sort']) && $_GET['sort'] === 'most_sold' ? "ORDER BY total_sold DESC" : "";

$filter_query = "SELECT * FROM products WHERE 1 ";
if ($filter_category) {
    $filter_query .= "AND category = '$filter_category' ";
}
if ($filter_status) {
    $filter_query .= "AND status = " . ($filter_status === 'active' ? 1 : 0) . " ";
}
$filter_query .= $order_by;
$result = mysqli_query($con, $filter_query);*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .filter-row {
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .table thead {
            background-color: #17a2b8;
            color: #ffffff;
        }
        .product_img {
            width: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center text-success mb-4">All Products</h1>

    <!-- Filter Section -->
    <div class="filter-row">
        <form class="row g-3" method="GET">
            <div class="col-md-4">
                <input type="text" name="product_category" class="form-control" placeholder="Filter by Product Category" />
            </div>
            <div class="col-md-4">
                <input type="text" name="product_most_sold" class="form-control" placeholder="Filter by Most Sold Product" />
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Filter by Status</option>
                    <option value="active">Active</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>
    </div>

   <!-- Products Table -->
   <div class="table-container">
            <table class="table table-hover table-bordered align-middle">
                <thead class="text-center">
                    <tr>
                        <th>Product Id</th>
                        <th>Product Title</th>
                        <th>Product Image</th>
                        <th>Product Price</th>
                        <th>Total Sold</th>
                        <th>Edit</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                    global $con;

                    $get_products = "SELECT * FROM products";
                    $result = mysqli_query($con, $get_products);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $product_id = $row['product_id'];
                        $product_title = $row['product_title'];
                        $product_image1 = $row['product_image1'];
                        $product_price = $row['product_price'];
                        $status = $row['status'];
                        ?>
                        <tr class='text-center'>
                            <td><?php echo $product_id; ?></td>
                            <td><?php echo $product_title; ?></td>
                            <td><img src='./product_images/<?php echo $product_image1; ?>' alt='Product Image' class='product_img'></td>
                            <td><?php echo $product_price; ?>/-</td>
                            <td>
                                <?php
                                    $get_count = "SELECT * FROM orders_pending WHERE product_id = $product_id";
                                    $result_count = mysqli_query($con, $get_count);
                                    echo mysqli_num_rows($result_count);
                                ?>
                            </td>
                            <td>
                                <a href='dashboard.php?edit_products=<?php echo $product_id; ?>' class='btn btn-sm btn-info'>
                                    <i class='fas fa-edit'></i> Edit
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $status ? 'success' : 'warning'; ?>">
                                    <?php echo $status ? 'Active' : 'On Hold'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $product_id; ?>">
                                    <?php if ($status): ?>
                                        <button type="submit" name="put_on_hold" class="btn btn-warning btn-sm btn-status">Put on Hold</button>
                                    <?php else: ?>
                                        <button type="submit" name="activate" class="btn btn-success btn-sm btn-status">Activate</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td>
                                <!-- Trigger the modal with a delete link -->
                                <a href='#' data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" onclick="setDeleteUrl('dashboard.php?delete_product=<?php echo $product_id; ?>')" class='btn btn-danger btn-sm'>
                                    <i class='fas fa-trash-alt'></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap Modal for Confirmation -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Set Delete URL -->
    <script>
        function setDeleteUrl(url) {
            document.getElementById('confirmDeleteBtn').setAttribute('href', url);
        }
    </script>

</body>
</html>