<?php
    if (isset($_GET['delete_user'])) {
    $delete_id_user = $_GET['delete_user'];

    // Check if the user ID is numeric
    if (!is_numeric($delete_id_user)) {
        echo "Invalid user ID.";
        exit;
    }

    // Check if there are any orders associated with this user
    $check_orders = "SELECT * FROM orders WHERE userid = $delete_id_user";
    $result_orders = mysqli_query($con, $check_orders);
    
    if (mysqli_num_rows($result_orders) > 0) {
        // If there are orders, prevent deletion and display a message
        echo "
        <div class='toast-container position-fixed top-50 start-50 translate-middle'>
            <div class='toast align-items-center text-bg-warning' role='alert' aria-live='assertive' aria-atomic='true'>
                <div class='d-flex'>
                    <div class='toast-body'>
                        Cannot delete user. The user has associated orders.
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
                        window.location.href = './dashboard.php?list_users'; // Redirect after 2 seconds
                    }, 2000);
            });
        </script>";
    } else {
        // If no orders are associated, proceed with the deletion
        $delete_user = "DELETE FROM user_table WHERE user_id = $delete_id_user";
        $result_user = mysqli_query($con, $delete_user);

        if ($result_user) {
            echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-success' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            User deleted successfully.
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
                        window.location.href = './dashboard.php?list_users'; // Redirect after 2 seconds
                    }, 2000);
                });
            </script>";
        } else {
            echo "
            <div class='toast-container position-fixed top-50 start-50 translate-middle'>
                <div class='toast align-items-center text-bg-danger' role='alert' aria-live='assertive' aria-atomic='true'>
                    <div class='d-flex'>
                        <div class='toast-body'>
                            Error deleting the user: " . mysqli_error($con) . "
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
                        window.location.href = './dashboard.php?list_users'; // Redirect after 2 seconds
                    }, 2000);
                });
            </script>";
        }
    }
}
?>
