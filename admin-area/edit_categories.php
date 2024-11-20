<?php
include('../includes/connect.php');

$category_title = "";
$edit_id_category = "";

// Check if 'edit_category' parameter is set in the URL
if (isset($_GET['edit_categories'])) {
    $edit_id_category = $_GET['edit_categories'];
    
    // Ensure 'edit_category' is a valid number (use intval to ensure it's an integer)
    $edit_id_category = intval($edit_id_category);

    if ($edit_id_category > 0) {
        // If valid category ID, retrieve category details
        $get_category = "SELECT * FROM categories WHERE category_id = $edit_id_category";
        $result = mysqli_query($con, $get_category);
        
        // Check if the query was successful
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $category_title = $row['category_title'];
            } else {
                // Handle case where no category is found
                echo "No category found with the given ID.";
            }
        } else {
            // Handle query error
            echo "Error retrieving category: " . mysqli_error($con);
        }
    } else {
        // Handle invalid category ID
        echo "Invalid category ID.";
    }
} else {
    echo "Category ID not specified.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<h3 class="text-center text-success">Edit category</h3>
<form method="POST"> <!-- Add a form tag -->
    <div class="form-outline mb-4 w-50 m-auto mb-4">
        <label for="product_category" class="from-label">Product category</label>
        <input type="text" name="category_title" class="form-control" required="required" value="<?php echo htmlspecialchars($category_title); ?>">
    </div>

    <!-- update -->
    <div class="form-outline mb-4 w-50 m-auto">
        <input type="submit" name="update_category" id="update_category" class="btn btn-info mb-3 px-3" value="Update category">
    </div>
</form> <!-- Close the form tag -->

<?php
// editing categories
if (isset($_POST['update_category'])) {
    $category_title = $_POST['category_title'];

    if (!empty($category_title) && $edit_id_category > 0) {
        // Ensure category title is safely escaped to avoid SQL injection
        $category_title = mysqli_real_escape_string($con, $category_title);

        // Update query
        $update_category = "UPDATE categories SET category_title = '$category_title' WHERE category_id = $edit_id_category";
        $result_update_category = mysqli_query($con, $update_category);

        if ($result_update_category) {
            echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-success' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            Category updated successfully!
                        </div>
                        <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                    </div>
                </div>
            </div>
            <script>
                var toast = new bootstrap.Toast(document.querySelector('.toast'));
                toast.show();
                setTimeout(function() {
                    window.open('./dashboard.php?view_categories', '_self');
                }, 2000); // Redirect after 2 seconds
            </script>";
        } else {
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
    } else {
        echo "Invalid category title or category ID.";
    }
}
?>

</body>
</html>
