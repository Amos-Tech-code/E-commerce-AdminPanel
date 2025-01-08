
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
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        <!-- Order Table -->
        <div class="filter-row mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" id="filterOrderId" class="form-control" placeholder="Filter by Order ID">
                </div>
                <div class="col-md-4">
                    <input type="text" id="filterUserId" class="form-control" placeholder="Filter by User ID">
                </div>
                <div class="col-md-4">
                    <select id="filterOrderStatus" class="form-select">
                        <option value="">Filter by Order Status</option>
                        <option value="pending">Pending</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-container table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>Order Id</th>
                        <th>User Id</th>
                        <th>Invoice Number</th>
                        <th>Total Amount</th>
                        <th>Order Status</th>
                        <th>Product Details</th>
                        <th>User Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $con;

                    $query = "
                        SELECT o.order_id, o.userid, o.invoice_number, o.total_amount, o.order_status
                        FROM orders o
                        ORDER BY o.order_date DESC
                    ";

                    $result = mysqli_query($con, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $order_id = $row['order_id'];
                        $user_id = $row['userid'];
                        $invoice_number = $row['invoice_number'];
                        $total_amount = $row['total_amount'];
                        $order_status = $row['order_status'];
                    ?>
                        <tr 
                            data-order-id="<?php echo $order_id; ?>" 
                            data-user-id="<?php echo $user_id; ?>" 
                            data-order-status="<?php echo strtolower($order_status); ?>" 
                            class="text-center"
                        >
                            <td>#<?php echo $order_id; ?></td>
                            <td><?php echo $user_id; ?></td>
                            <td><?php echo $invoice_number; ?></td>
                            <td>$<?php echo number_format($total_amount, 2); ?></td>
                            <td><?php echo ucfirst($order_status); ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="viewOrderProducts(<?php echo $order_id; ?>)">
                                    View Products
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewUserDetails(<?php echo $order_id; ?>)">
                                    <i class="fas fa-user"></i> View
                                </button>
                            </td>
                            <td>
                                <?php if ($order_status != 'Delivered' && $order_status != 'Cancelled'): ?>
                                    <button class="btn btn-success btn-sm" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'Delivered')">
                                        Mark as Complete
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'Cancelled')">
                                        Cancel
                                    </button>
                                <?php elseif ($order_status == 'Delivered'): ?>
                                    <span class="text-success">Completed</span>
                                <?php else: ?>
                                    <span class="text-danger">Cancelled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>


            <!-- Modals -->
            <div id="productDetailsModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Product Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="productDetailsContent"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="userDetailsModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">User Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="userDetailsContent"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

                </div>

                <script>
                    function viewOrderProducts(orderId) {
                fetch(`fetch_order_products.php?order_id=${orderId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('productDetailsContent').innerHTML = data;
                        new bootstrap.Modal(document.getElementById('productDetailsModal')).show();
                    });
            }

            function viewUserDetails(orderId) {
                fetch(`fetch_user_details.php?order_id=${orderId}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('userDetailsContent').innerHTML = data;
                        new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
                    });
            }


            function updateOrderStatus(orderId, status) {
                if (confirm(`Are you sure you want to mark this order as ${status}?`)) {
                    fetch(`update_order_status.php?order_id=${orderId}&status=${status}`)
                        .then(response => response.text())
                        .then(data => {
                            alert(data);
                            location.reload(); // Refresh the page to reflect changes
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Something went wrong!');
                        });
                }
            }


            </script>
            
            <!--Javascript funtion for filtering Orders-->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const filterOrderId = document.getElementById('filterOrderId');
                    const filterUserId = document.getElementById('filterUserId');
                    const filterOrderStatus = document.getElementById('filterOrderStatus');
                    const tableRows = document.querySelectorAll('tbody tr');

                    function filterTable() {
                        const orderIdValue = filterOrderId.value.trim().toLowerCase().replace('#', '');
                        const userIdValue = filterUserId.value.trim().toLowerCase();
                        const orderStatusValue = filterOrderStatus.value.trim().toLowerCase();

                        tableRows.forEach(row => {
                            const rowOrderId = row.dataset.orderId.toLowerCase();
                            const rowUserId = row.dataset.userId.toLowerCase();
                            const rowOrderStatus = row.dataset.orderStatus.toLowerCase();

                            const matchesOrderId = orderIdValue === '' || rowOrderId.includes(orderIdValue);
                            const matchesUserId = userIdValue === '' || rowUserId.includes(userIdValue);
                            const matchesOrderStatus = orderStatusValue === '' || rowOrderStatus === orderStatusValue;

                            if (matchesOrderId && matchesUserId && matchesOrderStatus) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }

                    // Attach event listeners
                    filterOrderId.addEventListener('input', filterTable);
                    filterUserId.addEventListener('input', filterTable);
                    filterOrderStatus.addEventListener('change', filterTable);
                });
            </script>



</body>
</html>
