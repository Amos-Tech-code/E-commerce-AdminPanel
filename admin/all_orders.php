<?php
include('../includes/connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
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
        .modal-content {
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="container mt-4">
        <h1 class="text-center text-success mb-4">All Orders</h1>

        <!-- Filter Section -->
        <div class="filter-row">
            <form class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="order_id" class="form-control" placeholder="Filter by Order ID" />
                </div>
                <div class="col-md-4">
                    <input type="text" name="user_id" class="form-control" placeholder="Filter by User ID" />
                </div>
                <div class="col-md-4">
                    <select name="order_status" class="form-select">
                        <option value="">Filter by Order Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>

        <!-- Order Table -->
        <div class="table-container">
            <table class="table table-hover table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>Order Id</th>
                        <th>User Id</th>
                        <th>Invoice Number</th>
                        <th>Product Id</th>
                        <th>Quantity</th>
                        <th>Order Status</th>
                        <th>User Details</th>
                        <th>Mark as Complete</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                    global $con;
                    $get_orders = "SELECT * FROM orders_pending";
                    $result = mysqli_query($con, $get_orders);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $order_id = $row['order_id'];
                        $user_id = $row['userid'];
                        $invoice_number = $row['invoice_number'];
                        $product_id = $row['product_id'];
                        $quantity = $row['quantity'];
                        $order_status = $row['order_status'];
                    ?>
                        <tr class="text-center">
                            <td><?php echo $order_id; ?></td>
                            <td><?php echo $user_id; ?></td>
                            <td><?php echo $invoice_number; ?></td>
                            <td><?php echo $product_id; ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td><?php echo ucfirst($order_status); ?></td>
                            <td>
                                <a href="dashboard.php?view_user=<?php echo $user_id; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-user"></i> View
                                </a>
                            </td>
                            <td>
                                <?php if ($order_status != 'completed'): ?>
                                    <a href="dashboard.php?complete_order=<?php echo $order_id; ?>" class="btn btn-success btn-sm">
                                        Mark as Complete
                                    </a>
                                <?php else: ?>
                                    <span class="text-success">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
