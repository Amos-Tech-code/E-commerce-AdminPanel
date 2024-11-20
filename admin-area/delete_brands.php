<?php
if(isset($_GET['delete_brand'])){
    $delete_id_brand=$_GET['delete_brand'];

    //delete query
    $delete_brand="delete from brands where brand_id=$delete_id_brand";
    $result_product=mysqli_query($con,$delete_brand);
    if($result_product){
        echo "
        <div class='toast-container position-fixed top-50 start-50 translate-middle'>
            <div class='toast align-items-center text-bg-success' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='d-flex'>
                    <div class='toast-body'>
                        Brand deleted successfully
                    </div>
                    <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toast = new bootstrap.Toast(document.querySelector('.toast'));
                toast.show();
                setTimeout(function() {
                    window.location.href = './dashboard.php?view_brands'; // Redirect after 2 seconds
                }, 2000);
            });
        </script>";
    } else{
        echo "
        <div class='toast-container position-fixed top-50 start-50 translate-middle'>
            <div class='toast align-items-center text-bg-danger' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='d-flex'>
                    <div class='toast-body'>
                        Brand deletion failed. Please try again.
                    </div>
                    <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toast = new bootstrap.Toast(document.querySelector('.toast'));
                toast.show();
            });
        </script>";
    }
}
?>