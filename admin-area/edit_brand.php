<?php
$brand_title="";
if(isset($_GET['edit_brands'])){
    $edit_id_brand=$_GET['edit_brands'];
    //echo $edit_id;
    $get_brand="select * from brands where brand_id=$edit_id_brand";
    $result=mysqli_query($con,$get_brand);
    $row=mysqli_fetch_assoc($result);
    $brand_title=$row['brand_title'];
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
<form method="POST"> <!-- Add a form tag -->
    <div class="form-outline mb-4 w-50 m-auto mb-4">
        <label for="product_brand" class="from-label">Product Brand</label>
        <input type="text" name="brand_title" class="form-control" required="required" value="<?php echo $brand_title; ?>">
    </div>

    <!-- update -->
    <div class="form-outline mb-4 w-50 m-auto">
        <input type="submit" name="update_brand" id="update_brand" class="btn btn-info mb-3 px-3" value="Update brand">
    </div>
</form> <!-- Close the form tag -->
</body>
</html>

<?php
// editing brands
if(isset($_POST['update_brand'])){
    $brand_title=$_POST['brand_title'];

    //query to update brand
    $update_brand="update brands set brand_title='$brand_title' where brand_id=$edit_id_brand"; // Note: Added single quotes around $brand_title
    $result_update_brand=mysqli_query($con,$update_brand);
    if($result_update_brand){
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
    } else{
        echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-danger' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            Error Failed to Update!
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
