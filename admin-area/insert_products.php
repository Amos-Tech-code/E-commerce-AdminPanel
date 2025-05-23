<?php
include('../includes/connect.php');
if(isset($_POST['insert_product'])){
    $product_title = mysqli_real_escape_string($con, $_POST['product_title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $product_keywords = mysqli_real_escape_string($con, $_POST['Product_keywords']);
    $product_category = mysqli_real_escape_string($con, $_POST['product_category']);
    $product_brands = mysqli_real_escape_string($con, $_POST['product_brands']);
    $product_price = mysqli_real_escape_string($con, $_POST['product_price']);
    $product_quantity = mysqli_real_escape_string($con, $_POST['product_quantity']);

    // Accessing images
    $product_image1 = $_FILES['product_image1']['name'];
    $product_image2 = $_FILES['product_image2']['name'];
    $product_image3 = $_FILES['product_image3']['name'];

    // Accessing image temp name
    $temp_image1 = $_FILES['product_image1']['tmp_name'];
    $temp_image2 = $_FILES['product_image2']['tmp_name'];
    $temp_image3 = $_FILES['product_image3']['tmp_name'];

    //checking empty condition
    if(empty($product_title) || empty($description) || empty($product_keywords) || empty($product_category) || empty($product_brands) || empty($product_price) || empty($product_image1) || empty($product_image2) || empty($product_image3) || empty($product_quantity)){
        $toast_message = 'Please fill all fields';
        $toast_class = 'text-bg-danger';
        exit();
    } else {
        move_uploaded_file($temp_image1, "./product_images/$product_image1");
        move_uploaded_file($temp_image2, "./product_images/$product_image2");
        move_uploaded_file($temp_image3, "./product_images/$product_image3");

        //insert query
        $insert_products = "INSERT INTO products (product_title, product_description, Product_keyword, category_id, brand_id, product_image1, product_image2, product_image3, product_price, quantity) VALUES ('$product_title', '$description', '$product_keywords', '$product_category', '$product_brands', '$product_image1', '$product_image2', '$product_image3', '$product_price', '$product_quantity')";
        $result_query = mysqli_query($con, $insert_products);
        
        // Show success or error message
        if($result_query){
            $toast_message = 'Successfully inserted the product';
            $toast_class = 'text-bg-success';
        } else {
            $toast_message = 'Something went wrong. Please try again.';
            $toast_class = 'text-bg-danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f4f6f9;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            color: #4caf50;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Insert Product</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <!-- Product Title -->
                <div class="col-md-6">
                    <label for="product_title" class="form-label">Product Title</label>
                    <input type="text" name="product_title" id="product_title" class="form-control" placeholder="Enter product title" autocomplete="off" required>
                </div>

                <!-- Product Description -->
                <div class="col-md-6">
                    <label for="description" class="form-label">Product Description</label>
                    <input type="text" name="description" id="description" class="form-control" placeholder="Enter product description" autocomplete="off" required>
                </div>

                <!-- Product Keywords -->
                <div class="col-md-6">
                    <label for="product_keywords" class="form-label">Product Keywords</label>
                    <input type="text" name="Product_keywords" id="product_keywords" class="form-control" placeholder="Enter product keywords" autocomplete="off" required>
                </div>

                <!-- Category -->
                <div class="col-md-6">
                    <label for="product_category" class="form-label">Category</label>
                    <select name="product_category" id="product_category" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php
                        $select_query = "SELECT * FROM categories";
                        $result_query = mysqli_query($con, $select_query);
                        while ($row = mysqli_fetch_assoc($result_query)) {
                            echo "<option value='" . $row['category_id'] . "'>" . $row['category_title'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Brand -->
                <div class="col-md-6">
                    <label for="product_brands" class="form-label">Brand</label>
                    <select name="product_brands" id="product_brands" class="form-select" required>
                        <option value="">Select Brand</option>
                        <?php
                        $select_query = "SELECT * FROM brands";
                        $result_query = mysqli_query($con, $select_query);
                        while ($row = mysqli_fetch_assoc($result_query)) {
                            echo "<option value='" . $row['brand_id'] . "'>" . $row['brand_title'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Product Images -->
                <div class="col-md-4">
                    <label for="product_image1" class="form-label">Product Image 1</label>
                    <input type="file" name="product_image1" id="product_image1" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="product_image2" class="form-label">Product Image 2</label>
                    <input type="file" name="product_image2" id="product_image2" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="product_image3" class="form-label">Product Image 3</label>
                    <input type="file" name="product_image3" id="product_image3" class="form-control">
                </div>

                <!-- Product Price -->
                <div class="col-md-6">
                    <label for="product_price" class="form-label">Product Price</label>
                    <input type="text" name="product_price" id="product_price" class="form-control" placeholder="Enter product price" autocomplete="off" required>
                </div>

                <!-- Product Quantity -->
                <div class="col-md-6">
                    <label for="product_quantity" class="form-label">Product Quantity</label>
                    <input type="number" name="product_quantity" id="product_quantity" class="form-control" placeholder="Enter quantity in stock" autocomplete="off" required>
                </div>

                <!-- Submit Button -->
                <div class="col-md-12">
                    <button type="submit" name="insert_product" class="btn btn-secondary w-100">Insert Product</button>
                </div>
            </div>
        </form>
    </div>

    <?php if (isset($toast_message)) { ?>
        <div class="toast-container">
            <div class="toast align-items-center <?= $toast_class ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $toast_message ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <script>
            var toast = new bootstrap.Toast(document.querySelector('.toast'));
            toast.show();
        </script>
    <?php } ?>
</body>
</html>
