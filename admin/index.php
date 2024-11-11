<?php
session_start();
include('../includes/connect.php');
include('../functions/common_functions.php');

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- bootstrap css link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- css file -->
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            border-radius: 8px;
            background-color: #ffffff;
            margin: 0 auto;
        }
        .img-container img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <h2 class="text-center mb-5">Admin Login</h2>
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-md-6 col-lg-5 img-container text-center mb-4 mb-md-0">
                <img src="../images/ecommerce.jpg" alt="" class="img-fluid">
            </div>
            <div class="col-lg-6 col-xl-5">
                <form action="" method="post">
                    <div class="form-outline mb-4">
                        <label for="username" class="form-label">Username</label><br>
                        <input type="text" id="admin_username" name="admin_username" placeholder="Enter your username" required="required" class="form-control m-auto">
                    </div>
                    <div class="form-outline mb-4">
                        <label for="password" class="form-label">Password</label><br>
                        <div class="input-group">
                            <input type="password" id="admin_password" name="admin_password" placeholder="Enter your password" required="required" class="form-control m-auto">
                            <button type="button" id="toggle-password" class="btn btn-outline-secondary">
                                <i id="password-icon" class="fas fa-eye"></i> <!-- Eye icon for password visibility toggle -->
                            </button>
                        </div>
                    </div>
                    <div class="d-grid">
                        <input type="submit" class="btn bg-info" name="admin_login" value="Login" id="admin_login">
                    </div>
                    <p>Don't have an account? <a href="admin_registration.php" class="text-danger mt-2">Register</a></p>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toastMessage" class="toast align-items-center text-white" role="alert" aria-live="assertive" aria-atomic="true" style="display: none;">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- bootstrap js link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Password toggle script -->
    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            // Get the password input and icon
            var passwordField = document.getElementById('admin_password');
            var passwordIcon = document.getElementById('password-icon');

            // Toggle password visibility
            if (passwordField.type === "password") {
                passwordField.type = "text"; // Show password
                passwordIcon.classList.remove('fa-eye'); // Change icon to eye-slash
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password"; // Hide password
                passwordIcon.classList.remove('fa-eye-slash'); // Change icon back to eye
                passwordIcon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>

<?php

if (isset($_POST['admin_login'])) {
    // Sanitize and validate inputs to prevent XSS and other security issues
    $admin_username = htmlspecialchars(trim($_POST['admin_username']), ENT_QUOTES, 'UTF-8');
    $admin_password = $_POST['admin_password']; // Don't sanitize passwords, they'll be hashed later

    // Prepare a SQL statement to prevent SQL injection
    $stmt = $con->prepare("SELECT * FROM admin_table WHERE admin_username = ?");
    $stmt->bind_param("s", $admin_username); // "s" means string parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $row_count = $result->num_rows;
    $row_data = $result->fetch_assoc();

    if ($row_count > 0) {
        // Verify password securely
        if (password_verify($admin_password, $row_data['admin_password'])) {
            $_SESSION['admin_username'] = $admin_username;
            $_SESSION['username'] = $admin_username;

            // Successful login toast
            echo "<script>
                    document.getElementById('toastBody').innerText = 'Login successful!';
                    document.getElementById('toastMessage').classList.add('bg-success');
                    document.getElementById('toastMessage').style.display = 'block';
                    var toast = new bootstrap.Toast(document.getElementById('toastMessage'));
                    toast.show();
                    setTimeout(function(){
                        window.location.href = './dashboard.php';
                    }, 2000);
                  </script>";
        } else {
            // Invalid password toast
            echo "<script>
                    document.getElementById('toastBody').innerText = 'Invalid Credentials!';
                    document.getElementById('toastMessage').classList.add('bg-danger');
                    document.getElementById('toastMessage').style.display = 'block';
                    var toast = new bootstrap.Toast(document.getElementById('toastMessage'));
                    toast.show();
                  </script>";
        }
    } else {
        // User not found toast
        echo "<script>
                document.getElementById('toastBody').innerText = 'Invalid Credentials!';
                document.getElementById('toastMessage').classList.add('bg-danger');
                document.getElementById('toastMessage').style.display = 'block';
                var toast = new bootstrap.Toast(document.getElementById('toastMessage'));
                toast.show();
              </script>";
    }

    // Close the prepared statement to release resources
    $stmt->close();
}
?>
