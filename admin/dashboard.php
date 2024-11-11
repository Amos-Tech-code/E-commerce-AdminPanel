<?php
session_start();

// Check for the admin session
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    header("Location: index.php"); // Redirect to login page if no admin session
    exit(); // Prevent further script execution
}

include('../includes/connect.php');
include('../functions/common_functions.php');

// Fetch data for dashboard statistics
$product_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM products");
$product_count = $product_result ? mysqli_fetch_array($product_result)['total'] : 0;

$user_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM user_table");
$user_count = $user_result ? mysqli_fetch_array($user_result)['total'] : 0;

$order_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM orders_pending");
$order_count = $order_result ? mysqli_fetch_array($order_result)['total'] : 0;

$brand_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM brands");
$brand_count = $brand_result ? mysqli_fetch_array($brand_result)['total'] : 0;

$category_result = mysqli_query($con, "SELECT COUNT(*) AS total FROM categories");
$category_count = $category_result ? mysqli_fetch_array($category_result)['total'] : 0;


// Fetch monthly order data for the chart
$order_data_query = "SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, COUNT(*) AS order_count
                     FROM user_orders
                     WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                     GROUP BY month
                     ORDER BY month";
$order_data_result = mysqli_query($con, $order_data_query);

$order_data = [];
$sales_data = [];
$months = [];

while ($row = mysqli_fetch_assoc($order_data_result)) {
    $months[] = $row['month'];
    $order_data[] = $row['order_count'];
    // Example static sales data; replace with actual sales data if available
    $sales_data[] = rand(5000, 20000); // Placeholder for sales data
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* Body and admin image styles */
    body {
        background-color: #f8f9fa;
    }

    .admin-image {
        width: 100px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Sidebar styles */
    .sidebar {
        background-color: #343a40;
        height: 100vh;
        overflow-y: auto;
    }

    /* Sidebar Link Hover and Active Styles */
    .sidebar a {
        color: white;
        padding: 12px;
        font-size: 1.1rem;
        display: block;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #0056b3; /* Hover background */
        color: white;
        padding-left: 20px; /* Slight indent on hover */
    }

    .sidebar a.active {
        background-color: #007bff; /* Active link background */
        color: white;
    }
    .sidebar .nav-icon {
    width: 25px;
    margin-right: 15px;
    }

    /* Main content area */
    .main-content {
        margin-left: 250px;
        padding: 20px;
    }

    /* Stats card styles */
    .stats-card {
        background-color: #007bff;
        color: white;
        border-radius: 10px;
        padding: 20px;
    }

    .stats-icon {
        font-size: 2rem;
    }

    /* Chart container styles */
    .chart-container {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
    }
   /* Style for the sidebar toggle button */
#sidebar-toggle {
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1050;
    display: block;
}

@media (max-width: 768px) {
    .sidebar {
        left: -250px; /* Initially hidden */
        transition: left 0.3s ease;
    }

    .sidebar.open {
        left: 0; /* Sidebar visible when open */
    }

    .main-content {
        margin-left: 0;
    }
}

</style>

</head>

    <!-- Sidebar -->
<div class="sidebar position-fixed d-none d-md-block" id="sidebar">
    <h4 class="text-center text-light py-3">Admin Panel</h4>
    <div class="p-3 text-center">
        <img src="../images/office.jpg" alt="Admin Image" class="admin-image mb-2">
        <p class="text-light">
            Admin <?php echo $_SESSION['admin_username']; ?>
        </p>

        <hr class="text-light">
        <a href="dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i> Overview</a>
        <a href="dashboard.php?chat_channels" class="nav-link"><i class="fas fa-comment-alt nav-icon"></i> Chat Channels</a>
        <a href="dashboard.php?insert_products" class="nav-link"><i class="fas fa-plus-circle nav-icon"></i> Insert Products</a>
        <a href="dashboard.php?view_products" class="nav-link"><i class="fas fa-boxes nav-icon"></i> View Products</a>
        <a href="dashboard.php?insert_category" class="nav-link"><i class="fas fa-th-large nav-icon"></i> Insert Categories</a>
        <a href="dashboard.php?view_categories" class="nav-link"><i class="fas fa-th-list nav-icon"></i> View Categories</a>
        <a href="dashboard.php?insert_brands" class="nav-link"><i class="fas fa-trademark nav-icon"></i> Insert Brands</a>
        <a href="dashboard.php?view_brands" class="nav-link"><i class="fas fa-cogs nav-icon"></i> View Brands</a>
        <a href="dashboard.php?all_orders" class="nav-link"><i class="fas fa-shopping-cart nav-icon"></i> All Orders</a>
        <a href="dashboard.php?list_users" class="nav-link"><i class="fas fa-users nav-icon"></i> List Users</a>
        <a href="admin_logout.php" class="nav-link"><i class="fas fa-sign-out-alt nav-icon"></i> Logout</a>
    </div>
</div>

<!-- Toggle Button for Mobile Devices -->
<button class="btn btn-primary d-md-none" id="sidebar-toggle">
    <i class="fas fa-bars"></i>
</button>

    <!-- Main Content -->
    <div class="main-content">
    <?php if (!isset($_GET['insert_products']) && !isset($_GET['view_products']) && !isset($_GET['insert_category']) && !isset($_GET['chat_channels']) && !isset($_GET['chat'])
    && !isset($_GET['view_categories']) && !isset($_GET['insert_brands']) && !isset($_GET['view_brands']) && !isset($_GET['edit_categories'])
    && !isset($_GET['all_orders']) && !isset($_GET['list_users']) && !isset($_GET['edit_products']) && !isset($_GET['edit_brands']) ): ?>

        <div class="container mt-5 pt-4">
            <h3 class="text-center text-success">Admin Dashboard Overview</h3>
            <p class="text-center text-success">Quick overview of the store's key metrics</p>

           <!-- Stats Cards -->
<div class="row g-3">
    <div class="col-md-3">
        <div class="stats-card text-center">
            <i class="fas fa-box-open stats-icon"></i>
            <h4>Products</h4>
            <h5><?php echo $product_count; ?></h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card text-center">
            <i class="fas fa-users stats-icon"></i>
            <h4>Users</h4>
            <h5><?php echo $user_count; ?></h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card text-center">
            <i class="fas fa-hourglass-half stats-icon"></i>
            <h4>Pending Orders</h4>
            <h5><?php echo $order_count; ?></h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card text-center">
            <i class="fas fa-trademark stats-icon"></i>
            <h4>Brands</h4>
            <h5><?php echo $brand_count; ?></h5>
        </div>
    </div>
    <!-- Category Card -->
    <div class="col-md-3">
        <div class="stats-card text-center">
            <i class="fas fa-th-large stats-icon"></i>
            <h4>Categories</h4>
            <h5><?php echo $category_count; ?></h5>
        </div>
    </div>
</div>

            <!-- Sales and Orders Chart -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="chart-container">
                        <h4>Sales & Orders Overview (Last 6 Months)</h4>
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Other Sections Based on Sidebar Links -->
        <div class="container my-3">
            <?php
            // Include different pages based on the selected option
            if (isset($_GET['insert_products'])) {
                include('insert_products.php');
            }
            if (isset($_GET['view_products'])) {
                include('view_products.php');
            }
            if (isset($_GET['insert_category'])) {
                include('insert_category.php');
            }
            if (isset($_GET['view_categories'])) {
                include('view_categories.php');
            }
            if (isset($_GET['insert_brands'])) {
                include('insert_brands.php');
            }
            if (isset($_GET['view_brands'])) {
                include('view_brands.php');
            }
            if (isset($_GET['all_orders'])) {
                include('all_orders.php');
            }
            if (isset($_GET['list_users'])) {
                include('list_users.php');
            }
            if (isset($_GET['edit_products'])) {
                include('edit_products.php');
            }
            if (isset($_GET['delete_product'])) {
                include('delete_product.php');
            }
            if (isset($_GET['edit_categories'])) {
                include('edit_categories.php');
            }
            if (isset($_GET['delete_category'])) {
                include('delete_categories.php');
            }
            if (isset($_GET['edit_brands'])) {
                include('edit_brand.php');
            }
            if (isset($_GET['delete_brand'])) {
                include('delete_brands.php');
            }
            if (isset($_GET['delete_user'])) {
                include('delete_users.php');
            }
            if (isset($_GET['chat_channels'])) {
                include('chat_channels.php');
            }
            if (isset($_GET['chat'])) {
                include('chat.php');
            }
            ?>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebar-toggle');

    if (sidebar && toggleButton) {
        toggleButton.addEventListener('click', function () {
            console.log("Sidebar toggle button clicked");  // For debugging
            sidebar.classList.toggle('open');
        });
    }
});

    </script>

            <!-- Bootstrap JS & Chart.js Initialization -->
            <script>
                const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'Orders',
                        data: <?php echo json_encode($order_data); ?>,
                        borderColor: '#28a745',
                        fill: false,
                    },
                    {
                        label: 'Sales ($)',
                        data: <?php echo json_encode($sales_data); ?>,
                        borderColor: '#007bff',
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false },
                },
            },
        });

    </script>


    <script>
        // Get all sidebar links
        const sidebarLinks = document.querySelectorAll('.sidebar a');

        // Get current page URL
        const currentUrl = window.location.href;

        // Loop through the links and check if the link href matches the current URL
        sidebarLinks.forEach(link => {
            if (currentUrl.includes(link.href)) {
                link.classList.add('active'); // Add 'active' class to the link
            } else {
                link.classList.remove('active'); // Remove 'active' class if not the current page
            }
        });
    </script>

</body>
</html>