<?php
include('../includes/connect.php');

        // Fetch categories for filter
        $category_query = "SELECT * FROM categories";
        $category_result = mysqli_query($con, $category_query);


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
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            <form class="row g-3" id="filterForm">
                <div class="col-md-4">
                    <select name="category_id" class="form-select" id="categoryFilter">
                        <option value="">Filter by Category</option>
                        <?php while ($row = mysqli_fetch_assoc($category_result)): ?>
                            <option value="<?= $row['category_id']; ?>"><?= $row['category_title']; ?></option>
                        <?php endwhile; ?>
                    </select>            
                </div>
                <div class="col-md-4">
                    <input type="text" name="product_most_sold" class="form-control" id="mostSoldFilter" placeholder="Filter by No. of Ordered Products" />
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select" id="statusFilter">
                        <option value="">Filter by Status</option>
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                    </select>
                </div>
            </form>
        </div>


   <!-- Products Table -->
   <div class="table-container table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="text-center">
                <tr>
                    <th>Product Id</th>
                    <th>Product Title</th>
                    <th>Product Image</th>
                    <th>Product Price</th>
                    <th>Total Ordered</th>
                    <th>Edit</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <th>Delete</th>
                </tr>
            </thead>
            
            <tbody id="productTable">
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
                // Get total orders for each product
                $get_count = "SELECT * FROM order_items WHERE product_id = $product_id";
                $result_count = mysqli_query($con, $get_count);
                $total_ordered = mysqli_num_rows($result_count);
            ?>
                <tr class="text-center" data-id="<?= $product_id; ?>" data-category-id="<?= $row['category_id'] ?>" data-status="<?= $row['status'] ?>" data-title="<?= $row['product_title'] ?>">
                    <td data-key="product_id"><?= $product_id; ?></td>
                    <td data-key="product_title"><?= $product_title; ?></td>
                    <td>
                        <img src="./product_images/<?php echo $product_image1; ?>" alt="Product Image" class="product_img">
                    </td>
                    <td data-key="product_price"><?= $product_price; ?>/-</td>
                    <td data-key="total_ordered"><?= $total_ordered; ?></td>
                    <td>
                        <a href="dashboard.php?edit_products=<?php echo $product_id; ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                    <td data-key="status">
                        <span class="badge bg-<?php echo $status ? 'success' : 'warning'; ?>">
                            <?php echo $status ? 'Active' : 'On Hold'; ?>
                        </span>
                    </td>
                    <td data-key="actions">
                        <button onclick="updateStatus(<?= $product_id; ?>, <?= $status ? 0 : 1; ?>)" 
                             class="btn btn-<?= $status ? 'warning' : 'success'; ?> btn-sm">
                            <?= $status ? 'Put on Hold' : 'Activate'; ?>
                        </button>
                    </td>
                    <td>
                        <button onclick="deleteProduct(<?= $product_id; ?>)" 
                                class="btn btn-danger btn-sm">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
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
                            <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
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

    <script>
        // JavaScript for Dynamic Table Filtering
    document.addEventListener('DOMContentLoaded', () => {
        const categoryFilter = document.getElementById('categoryFilter');
        const mostSoldFilter = document.getElementById('mostSoldFilter');
        const statusFilter = document.getElementById('statusFilter');
        const productTable = document.getElementById('productTable');
        const products = Array.from(productTable.getElementsByTagName('tr'));

        function filterProducts() {
            const categoryId = categoryFilter.value;
            const status = statusFilter.value;
            const mostSold = mostSoldFilter.value.toLowerCase();

            products.forEach(productRow => {
                const category = productRow.getAttribute('data-category-id');
                const statusValue = productRow.getAttribute('data-status');
                const title = productRow.getAttribute('data-title').toLowerCase();

                const matchesCategory = categoryId ? category == categoryId : true;
                const matchesStatus = status ? (status === 'active' ? statusValue == 1 : statusValue == 0) : true;
                const matchesMostSold = mostSold ? title.includes(mostSold) : true;

                if (matchesCategory && matchesStatus && matchesMostSold) {
                    productRow.style.display = '';
                } else {
                    productRow.style.display = 'none';
                }
            });
        }

        // Apply filter when any of the filters change
        categoryFilter.addEventListener('change', filterProducts);
        statusFilter.addEventListener('change', filterProducts);
        mostSoldFilter.addEventListener('input', filterProducts);
    });


    function deleteProduct(productId) {
    // Open the modal
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    confirmDeleteModal.show();

    // Set the product ID dynamically in the modal's delete button
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    confirmDeleteBtn.onclick = () => {
        // Trigger the delete action
        fetch(`delete_product.php?delete_product=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    document.querySelector(`tr[data-id="${productId}"]`).remove();

                    // Show success toast
                    showToast(data.message, 'success');
                } else {
                    // Show error toast
                    showToast(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'danger');
            })
            .finally(() => {
                // Close the modal
                confirmDeleteModal.hide();
            });
    };
}


// Helper function to display Bootstrap toasts dynamically
function showToast(message, type) {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-50 start-50 translate-middle';
    toastContainer.innerHTML = `
        <div class="toast align-items-center text-bg-${type}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    document.body.appendChild(toastContainer);

    // Initialize and show the toast
    const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
    toast.show();

    // Remove the toast after it's hidden
    toastContainer.addEventListener('hidden.bs.toast', () => toastContainer.remove());
}


function updateStatus(productId, newStatus) {
    fetch('update_product_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            status: newStatus,
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the status badge and button dynamically
                const row = document.querySelector(`tr[data-id="${productId}"]`);
                const statusCell = row.querySelector('[data-key="status"]');
                const actionCell = row.querySelector('[data-key="actions"]');

                // Update status badge
                statusCell.innerHTML = `
                    <span class="badge bg-${newStatus ? 'success' : 'warning'}">
                        ${newStatus ? 'Active' : 'On Hold'}
                    </span>
                `;

                // Update action button
                actionCell.innerHTML = `
                    <button onclick="updateStatus(${productId}, ${newStatus ? 0 : 1})" 
                            class="btn btn-${newStatus ? 'warning' : 'success'} btn-sm">
                        ${newStatus ? 'Put on Hold' : 'Activate'}
                    </button>
                `;

                // Show success toast
                showToast(data.message, 'success');
            } else {
                // Show error toast
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again:', 'danger');
        });
}


</script>

</body>
</html>