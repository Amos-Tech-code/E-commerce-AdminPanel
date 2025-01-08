<?php
$brand_title = "";
if (isset($_GET['edit_brands'])) {
    $edit_id_brand = (int)$_GET['edit_brands']; // Typecasting to integer for added safety

    // Fetch the brand using a parameterized query
    $get_brand = "SELECT * FROM brands WHERE brand_id=$edit_id_brand";
    $result = mysqli_query($con, $get_brand);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $brand_title = $row['brand_title'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Brand</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<h3 class="text-center text-success">Edit Brand</h3>
<form method="POST">
    <div class="form-outline mb-4 w-50 m-auto mb-4">
        <label for="product_brand" class="from-label">Product Brand</label>
        <input type="text" name="brand_title" class="form-control" required="required" 
            value="<?php echo htmlspecialchars($brand_title, ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <!-- Update button -->
    <div class="form-outline mb-4 w-50 m-auto">
        <input type="submit" name="update_brand" id="update_brand" class="btn btn-info mb-3 px-3" value="Update brand">
    </div>
</form>
</body>
</html>

<?php
// Editing brands
if (isset($_POST['update_brand'])) {
    // Sanitize brand title
    $brand_title = mysqli_real_escape_string($con, $_POST['brand_title']);

    // Query to update the brand
    $update_brand = "UPDATE brands SET brand_title='$brand_title' WHERE brand_id=$edit_id_brand";
    $result_update_brand = mysqli_query($con, $update_brand);

    if ($result_update_brand) {
        echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-success' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            Brand updated successfully!
                        </div>
                        <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                    </div>
                </div>
            </div>
            <script>
                var toast = new bootstrap.Toast(document.querySelector('.toast'));
                toast.show();
                setTimeout(function() {
                    window.open('./dashboard.php?view_brands', '_self');
                }, 2000); // Redirect after 2 seconds
            </script>";
    } else {
        echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-danger' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            Error: Failed to update the brand!
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
