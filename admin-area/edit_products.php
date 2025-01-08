<?php
// Initialize variables with default values
$product_title = "";
$product_description = "";
$product_keyword = "";
$category_id = "";
$brand_id = "";
$product_image1 = "";
$product_image2 = "";
$product_image3 = "";
$product_price = "";
$product_quantity = "";

if(isset($_GET['edit_products'])){
    $edit_id=$_GET['edit_products'];
    //echo $edit_id;
    $get_data="select * from products where product_id=$edit_id";
    $result=mysqli_query($con,$get_data);
    $row=mysqli_fetch_assoc($result);
    $product_title=$row['product_title'];
    //echo $product_title;
    $product_description=$row['product_description'];
    $product_keyword=$row['Product_keyword'];
    $category_id=$row['category_id'];
    $brand_id=$row['brand_id'];
    $product_image1=$row['product_image1'];
    $product_image2=$row['product_image2'];
    $product_image3=$row['product_image3'];
    $product_price=$row['product_price'];
    $product_quantity=$row['quantity'];
}

//fetching category name if category_id is not empty
if (!empty($category_id)) {
    $select_category="select * from categories where category_id=$category_id";
    $result_category=mysqli_query($con,$select_category);
    $row_category=mysqli_fetch_assoc($result_category);
    $category_title=$row_category['category_title'];
    //echo $category_title;
}

//fetching brand name if brand_id is not empty
if (!empty($brand_id)) {
    $select_brand="select * from brands where brand_id=$brand_id";
    $result_brand=mysqli_query($con,$select_brand);
    $row_brand=mysqli_fetch_assoc($result_brand);
    $brand_title=$row_brand['brand_title'];
    //echo $brand_title;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        img {
            width: 100px;
    object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-success">Edit Product</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <!-- title -->
            <div class="form-outline mb-4 w-50 m-auto mb-4">
                <label for="product_title" class="from-label">Product Title</label>
                <input type="text" name="product_title" class="form-control" required="required" value="<?php echo $product_title; ?>">
            </div>
            <!-- description -->
            <div class="form-outline mb-4 w-50 m-auto mb-4">
                <label for="product_description" class="from-label">Product Description</label>
                <input type="text" name="product_description" value="<?php echo $product_description; ?>" class="form-control" required="required" >
            </div>
            <!-- keyword -->
            <div class="form-outline mb-4 w-50 m-auto mb-4">
                <label for="product_keyword" class="from-label">Product Keyword</label>
                <input type="text" name="product_keyword" class="form-control" required="required">
            </div>
            <!-- categories from the database -->
            <div class="form-outline mb-4 w-50 m-auto">
            <label for="Product_image2" class="form-label"><h5>Category</h5></label>
    <select name="product_category" id="" class="form-select">
        <?php if (isset($category_title)) { ?>
            <option value="<?php echo $category_title ?>"><?php echo $category_title ?></option>
        <?php } else { ?>
            <option value="">Category Not Available</option>
        <?php } ?>
        <?php
        $select_category_all="select * from categories";
        $result_category_all=mysqli_query($con,$select_category_all);
        while($row_category_all=mysqli_fetch_assoc($result_category_all)){
            $category_title=$row_category_all['category_title'];
            $category_id=$row_category_all['category_id'];
            echo "<option value='$category_id'>$category_title</option>'";
        };
        ?>
    </select>
</div>

<!-- brands -->
<div class="form-outline mb-4 w-50 m-auto">
<label for="Product_image2" class="form-label"><h5>Brand</h5></label>
    <select name="product_brand" id="" class="form-select">
        <?php if (isset($brand_title)) { ?>
            <option value="<?php echo $brand_title ?>"><?php echo $brand_title ?></option>
        <?php } else { ?>
            <option value="">Brand Not Available</option>
        <?php } ?>
        <?php
        $select_brand_all="select * from brands";
        $result_brand_all=mysqli_query($con,$select_brand_all);
        while($row_brand_all=mysqli_fetch_assoc($result_brand_all)){
            $brand_title=$row_brand_all['brand_title'];
            $brand_id=$row_brand_all['brand_id'];
            echo "<option value='$brand_id'>$brand_title</option>'";
        };
        ?>
    </select>
</div>

            <!-- Image 1 -->
            <div class="form-outline mb-4 w-50 m-auto d-flex">
                <label for="Product_image1" class="form-label"><h5>Product image 1</h5></label>
                <input type="file" name="product_image1" id="product_image1" class="form-control" >
                <img src="./product_images/<?php echo $product_image1; ?>" alt="">
            </div>
            <!-- Image 2 -->
            <div class="form-outline mb-4 w-50 m-auto d-flex">
                <label for="Product_image2" class="form-label"><h5>Product image 2</h5></label>
                <input type="file" name="product_image2" id="product_image2" class="form-control" >
                <img src="./product_images/<?php echo $product_image2; ?>" alt="">
            </div>
            <!-- Image 3 -->
            <div class="form-outline mb-4 w-50 m-auto d-flex">
                <label for="Product_image3" class="form-label"><h5>Product image 3</h5></label>
                <input type="file" name="product_image3" id="product_image3" class="form-control" >
                <img src="./product_images/<?php echo $product_image3; ?>" alt="">
            </div>
            <!-- price -->
            <div class="form-outline mb-4 w-50 m-auto">
                <label for="product_price" class="form-label"><h5>Product price</h5></label>
                <input type="text" name="product_price" value="<?php echo $product_price; ?>" id="product_price" class="form-control" placeholder="Enter product price" autocomplete="off" required="required">
            </div>
                    <!-- quantity -->
        <div class="form-outline mb-4 w-50 m-auto">
            <label for="product_quantity" class="form-label"><h5>Product Quantity</h5></label>
            <input type="number" name="product_quantity" value="<?php echo $product_quantity; ?>" id="product_quantity" class="form-control" placeholder="Enter quantity In Stock" autocomplete="off" required="required">
        </div>
            <!-- update -->
            <div class="form-outline mb-4 w-50 m-auto">
            <input type="submit" name="edit_product" id="edit_product" class="btn btn-info mb-3 px-3" value="Update Product">
            </div>
        </form>
    </div>
</body>
</html>
<?php
if (isset($_POST['edit_product'])) {
    $product_title = $_POST['product_title'];
    $product_description = $_POST['product_description'];
    $product_keyword = $_POST['product_keyword'];
    $product_category = $_POST['product_category'];
    $product_brand = $_POST['product_brand'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];

    // Check for new image uploads; if none, keep the existing image
    $product_image1 = $_FILES['product_image1']['name'] ? $_FILES['product_image1']['name'] : $row['product_image1'];
    $product_image2 = $_FILES['product_image2']['name'] ? $_FILES['product_image2']['name'] : $row['product_image2'];
    $product_image3 = $_FILES['product_image3']['name'] ? $_FILES['product_image3']['name'] : $row['product_image3'];

    // Temporary file paths
    $temp_image1 = $_FILES['product_image1']['tmp_name'];
    $temp_image2 = $_FILES['product_image2']['tmp_name'];
    $temp_image3 = $_FILES['product_image3']['tmp_name'];

    // Only move the uploaded file if a new image is provided
    if (!empty($temp_image1)) {
        move_uploaded_file($temp_image1, "./product_images/$product_image1");
    }
    if (!empty($temp_image2)) {
        move_uploaded_file($temp_image2, "./product_images/$product_image2");
    }
    if (!empty($temp_image3)) {
        move_uploaded_file($temp_image3, "./product_images/$product_image3");
    }

    // Query to update the product
    // Escape special characters in input values
    $product_title = mysqli_real_escape_string($con, $product_title);
    $product_description = mysqli_real_escape_string($con, $product_description);
    $product_keyword = mysqli_real_escape_string($con, $product_keyword);
    $product_category = mysqli_real_escape_string($con, $product_category);
    $product_brand = mysqli_real_escape_string($con, $product_brand);
    $product_price = mysqli_real_escape_string($con, $product_price);
    $product_quantity = mysqli_real_escape_string($con, $product_quantity);

    // Query to update the product
    $update_product = "UPDATE products SET 
        product_title='$product_title',
        product_description='$product_description',
        Product_keyword='$product_keyword',
        category_id='$product_category',
        brand_id='$product_brand',
        product_image1='$product_image1',
        product_image2='$product_image2',
        product_image3='$product_image3',
        product_price='$product_price',
        quantity='$product_quantity'
    WHERE product_id=$edit_id";

    $result_update = mysqli_query($con, $update_product);

    if($result_update){
        echo "
        <div class='toast-container position-fixed top-50 start-50 translate-middle'>
            <div class='toast align-items-center text-bg-success' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='d-flex'>
                    <div class='toast-body'>
                        Product updated successfully!
                    </div>
                    <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                </div>
            </div>
        </div>
        <script>
            var toast = new bootstrap.Toast(document.querySelector('.toast'));
            toast.show();
            setTimeout(function(){
                window.open('./dashboard.php?view_products','_self');
            }, 2000); // Redirect after 2 seconds
        </script>";
    } else {
        echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-danger' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            Product update failed. Please try again.
                        </div>
                        <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                    </div>
                </div>
            </div>
            <script>
                var toast = new bootstrap.Toast(document.querySelector('.toast'));
                toast.show();
            </script>";
    }

}
?>