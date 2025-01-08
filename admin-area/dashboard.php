<?php
session_start();

// Check for the admin session
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username']) || empty($_SESSION['admin_id']) ) {
    header("Location: index.php"); // Redirect to login page if no admin session
    exit(); // Prevent further script execution
}

include('../includes/connect.php');
include('../functions/common_functions.php');
include('dashboard_overview_statistics.php')

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
    <!-- Bootstrap JS (for modal functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
        z-index: 1050;
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
    margin-top: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            position: fixed;
            top: 0;
            left: -250px; /* Ensure the sidebar is hidden off-screen */
            width: 250px; /* Sidebar width */
            height: 100vh; /* Full height */
            transition: left 0.3s ease; /* Smooth transition */
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
    <div class="sidebar position-fixed" id="sidebar">
        <h4 class="text-center text-light py-3">Admin Panel</h4>
        <div class="p-3 text-center">
            <img src="../images/ecommerce.jpg" alt="Admin Image" class="admin-image mb-2">
            <p class="text-light">
                Admin <?php echo $_SESSION['admin_username']; ?>
            </p>

            <hr class="text-light">
            <a href="dashboard" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i> Overview</a>
            <a href="dashboard?all_orders" class="nav-link"><i class="fas fa-shopping-cart nav-icon"></i> All Orders</a>
            <a href="dashboard?chat_channels" class="nav-link"><i class="fas fa-comment-alt nav-icon"></i> Chat Channels</a>
            <a href="dashboard?insert_products" class="nav-link"><i class="fas fa-plus-circle nav-icon"></i> Insert Products</a>
            <a href="dashboard?view_products" class="nav-link"><i class="fas fa-boxes nav-icon"></i> View Products</a>
            <a href="dashboard?insert_category" class="nav-link"><i class="fas fa-th-large nav-icon"></i> Insert Categories</a>
            <a href="dashboard?view_categories" class="nav-link"><i class="fas fa-th-list nav-icon"></i> View Categories</a>
            <a href="dashboard?insert_brands" class="nav-link"><i class="fas fa-trademark nav-icon"></i> Insert Brands</a>
            <a href="dashboard?view_brands" class="nav-link"><i class="fas fa-th-list nav-icon"></i> View Brands</a>
            <a href="dashboard?settings_page" class="nav-link">
            <i class="fas fa-cogs nav-icon"></i> Settings
            </a>
            <a href="dashboard?list_users" class="nav-link"><i class="fas fa-users nav-icon"></i> List Users</a>
            <!-- Logout Button that triggers the modal -->
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fas fa-sign-out-alt nav-icon"></i> Logout
            </a>
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
    && !isset($_GET['all_orders']) && !isset($_GET['list_users']) && !isset($_GET['edit_products']) && !isset($_GET['settings_page']) && !isset($_GET['edit_brands']) ): ?>

        <div class="container mt-5 pt-4">
            <h3 class="text-center text-success">Admin Dashboard Overview</h3>
            <p class="text-center text-success">Quick overview of the store's key metrics</p>

           <!-- Stats Cards -->
             <!-- Revenue Insights -->

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                    <i class="fas fa-dollar-sign stats-icon"></i>
                        <h4>Total Revenue</h4>
                        <h5><?php echo number_format($total_revenue, 2); ?> USD</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-chart-line stats-icon"></i>
                        <h4>Monthly Revenue</h4>
                        <h5><?php echo number_format($monthly_revenue, 2); ?> USD</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-star stats-icon"></i>
                        <h4>Top Product</h4>
                        <h5><?php echo $top_product; ?></h5>
                    </div>
                </div>
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

            <!-- Category and Brand Charts Revenue Distribution-->
            <div class="row mt-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4>Revenue Distribution by Category</h4>
                    <canvas id="categoryRevenueChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4>Revenue Distribution by Brand</h4>
                    <canvas id="brandRevenueChart"></canvas>
                </div>
            </div>
            </div>
            <!-- Pie charts for Order Status & Customer Segmentation -->
            <div class="row mt-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4>Proportion of order statuses</h4>
                    <canvas id="orderStatusPieChart" width="400" height="400"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h4>Customer Segmentation</h4>
                    <canvas id="customerSegmentationPieChart" width="400" height="400"></canvas>
                </div>
            </div>
            </div>

            <!-- Sales and Orders Chart -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="chart-container">
                        <h4>Sales & Orders Overview (Last 6 Months)</h4>
                        <canvas id="monthlyOrdersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="mt-5">
                <h4>Recent Orders</h4>
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = mysqli_query($con, $recent_orders);
                        while ($order = mysqli_fetch_assoc($orders)) {
                            echo "<tr>
                                    <td>{$order['order_id']}</td>
                                    <td>{$order['userid']}</td>
                                    <td>{$order['total_amount']} USD</td>
                                    <td>{$order['order_status']}</td>
                                    <td>{$order['order_date']}</td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        
        </div>
        <?php endif; ?>

        <!-- Other Sections Based on Sidebar Links -->
        <div class="container my-3" id="dynamic-content">
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
            if (isset($_GET['update_product_status'])) {
                include('update_product_status.php');
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
            if (isset($_GET['settings_page'])) {
                include('settings_page.php');
            };
            ?>
        </div>
    </div>


    <!-- Logout Confirmation Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to logout?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <!-- Redirect to admin_logout.php on confirmation -->
                        <a href="admin_logout" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>

            <script>
            //Side bar toggle    
           document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.getElementById('sidebar');
                const toggleButton = document.getElementById('sidebar-toggle');

                if (sidebar && toggleButton) {
                   // console.log("Sidebar and toggle button initialized."); // Debug log
                    toggleButton.addEventListener('click', function () {
                       // console.log("Toggle button clicked."); // Debug log
                        sidebar.classList.toggle('open');
                       // console.log("Sidebar classes:", sidebar.classList); // Debug log
                    });
                } else {
                   // console.error("Sidebar or toggle button not found!");
                }
            });


            </script>

            <!-- Bootstrap JS & Chart.js Initialization for last six months(sales and orders)-->
            <script>
                    // Monthly Orders and Sales Chart
            const ctx = document.getElementById('monthlyOrdersChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo $month_labels; ?>, // Human-readable month names
                    datasets: [
                        {
                            label: 'Order Count',
                            data: <?php echo json_encode($order_data); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)', // Light Red
                            borderColor: 'rgba(255, 99, 132, 1)',       // Bold Red
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Total Sales (USD)',
                            data: <?php echo json_encode($sales_data); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Light Blue
                            borderColor: 'rgba(54, 162, 235, 1)',       // Bold Blue
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                font: {
                                    size: 14,
                                },
                            },
                        },
                        tooltip: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month',
                                font: {
                                    size: 14,
                                    weight: 'bold',
                                },
                            },
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Order Count',
                            },
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Sales (USD)',
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        },
                    },
                },
            });


            </script>

            <script>
            // Revenue Distribution by Category
            const categoryCtx = document.getElementById('categoryRevenueChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($categories); ?>,
                    datasets: [{
                        label: 'Revenue (USD)',
                        data: <?php echo json_encode($categoryRevenue); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                }
            });

            // Revenue Distribution by Brand
            const brandCtx = document.getElementById('brandRevenueChart').getContext('2d');
            new Chart(brandCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($brands); ?>,
                    datasets: [{
                        label: 'Revenue (USD)',
                        data: <?php echo json_encode($brandRevenue); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                }
            });

            </script>

            <script>
                //Pie Chart for order status
                const ctxPie = document.getElementById('orderStatusPieChart').getContext('2d');
                new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: <?php echo $order_status_labels; ?>, // Order statuses
                        datasets: [{
                            data: <?php echo $order_status_data; ?>,  // Count of orders per status
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',  // Red
                                'rgba(54, 162, 235, 0.7)',  // Blue
                                'rgba(255, 206, 86, 0.7)',  // Yellow
                                'rgba(75, 192, 192, 0.7)',  // Green
                                'rgba(153, 102, 255, 0.7)', // Purple
                                'rgba(255, 159, 64, 0.7)',  // Orange
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                            ],
                            borderWidth: 1,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 14,
                                    },
                                },
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function (context) {
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(2);
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    },
                                },
                            },
                        },
                    },
                });

            </script>
            <script>
                //Pie chart for customer segmentation
                const ctxCustomerPie = document.getElementById('customerSegmentationPieChart').getContext('2d');
                new Chart(ctxCustomerPie, {
                    type: 'pie',
                    data: {
                        labels: <?php echo $customer_segmentation_labels; ?>, // Spending categories
                        datasets: [{
                            data: <?php echo $customer_segmentation_counts; ?>, // Count of customers per segment
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.7)',  // Green
                                'rgba(255, 205, 86, 0.7)',  // Yellow
                                'rgba(255, 99, 132, 0.7)',  // Red
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(255, 99, 132, 1)',
                            ],
                            borderWidth: 1,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 14,
                                    },
                                },
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function (context) {
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(2);
                                        return `${context.label}: ${value} (${percentage}%)`;
                                    },
                                },
                            },
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