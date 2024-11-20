<?php
    include('../includes/connect.php');
    include('../functions/common_functions.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <!-- Custom CSS -->
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
        .input-group .btn {
            border-radius: 0.375rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-5">
        <h2 class="text-center mb-5">Admin Registration</h2>
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-md-6 col-lg-5 img-container text-center mb-4 mb-md-0">
                <img src="../images/ecommerce.jpg" alt="E-commerce" class="img-fluid rounded">
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="form-container">
                    <form action="" method="post">
                        <div class="mb-4">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="admin_username" name="admin_username" placeholder="Enter your username" required class="form-control">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="admin_email" name="admin_email" placeholder="Enter your email" required class="form-control">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" id="admin_password" name="admin_password" placeholder="Enter your password" required class="form-control">
                                <button type="button" id="toggle-password" class="btn btn-outline-secondary">
                                    <i id="password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" id="admin_confirm_password" name="admin_confirm_password" placeholder="Confirm password" required class="form-control">
                                <button type="button" id="toggle-confirm-password" class="btn btn-outline-secondary">
                                    <i id="confirm-password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-grid">
                            <input type="submit" class="btn btn-info" name="admin_registration" value="Register">
                        </div>
                        <p class="text-center mt-3">Already have an account? <a href="index.php" class="text-primary">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toast-message"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Password Toggle Script -->
    <script>
        function togglePasswordVisibility(passwordFieldId, iconId) {
            var passwordField = document.getElementById(passwordFieldId);
            var passwordIcon = document.getElementById(iconId);

            if (passwordField.type === "password") {
                passwordField.type = "text"; // Show password
                passwordIcon.classList.remove('fa-eye'); // Change icon to eye-slash
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password"; // Hide password
                passwordIcon.classList.remove('fa-eye-slash'); // Change icon back to eye
                passwordIcon.classList.add('fa-eye');
            }
        }

        document.getElementById('toggle-password').addEventListener('click', function() {
            togglePasswordVisibility('admin_password', 'password-icon');
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            togglePasswordVisibility('admin_confirm_password', 'confirm-password-icon');
        });
    </script>
</body>
</html>


<!-- PHP Code -->
<?php
if (isset($_POST['admin_registration'])) {
    // Sanitize user inputs to prevent XSS and SQL injection
    $admin_username = htmlspecialchars(trim($_POST['admin_username']), ENT_QUOTES, 'UTF-8');
    $admin_email = filter_var(trim($_POST['admin_email']), FILTER_SANITIZE_EMAIL);  // Sanitize the email
    $admin_password = $_POST['admin_password'];
    $admin_confirm_password = $_POST['admin_confirm_password'];

    // Validate email format
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
        $toast_class = "bg-danger";  // Red for error message
    } elseif ($admin_password != $admin_confirm_password) {
        $message = "Passwords do not match";
        $toast_class = "bg-danger";  // Red for error message
    } else {
        // Secure query: prepared statements to prevent SQL injection
        $select_query = "SELECT * FROM admin_table WHERE admin_username = ? OR admin_email = ?";
        if ($stmt = $con->prepare($select_query)) {
            $stmt->bind_param("ss", $admin_username, $admin_email); // Bind parameters
            $stmt->execute();
            $result = $stmt->get_result();
            $rows_count = $result->num_rows;
            $stmt->close();
        }

        if ($rows_count > 0) {
            $message = "Username or email already exists";
            $toast_class = "bg-danger";  // Red for error message
        } else {
            // Hash the password securely
            $hash_password = password_hash($admin_password, PASSWORD_DEFAULT);
            
            // Insert query with prepared statement
            $insert_query = "INSERT INTO admin_table (admin_username, admin_email, admin_password) VALUES (?, ?, ?)";
            if ($stmt = $con->prepare($insert_query)) {
                $stmt->bind_param("sss", $admin_username, $admin_email, $hash_password);
                $sql_execute = $stmt->execute();
                $stmt->close();

                if ($sql_execute) {
                    $message = "Registration successful, you can now login.";
                    $toast_class = "bg-success";  // Green for success message
                    // Redirect after 3 seconds
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var toastEl = document.getElementById('liveToast');
                            var toastMessage = document.getElementById('toast-message');
                            toastMessage.textContent = '$message';
                            toastEl.classList.add('$toast_class'); // Add the toast class for color styling
                            var toast = new bootstrap.Toast(toastEl);
                            toast.show();
                            setTimeout(function() {
                                window.location.href = './index.php'; // Redirect after 2 seconds
                            }, 2000); // 2000 milliseconds = 2 seconds
                        });
                    </script>";
                } else {
                    $message = "Registration failed: " . mysqli_error($con);
                    $toast_class = "bg-danger";  // Red for error message
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var toastEl = document.getElementById('liveToast');
                            var toastMessage = document.getElementById('toast-message');
                            toastMessage.textContent = '$message';
                            toastEl.classList.add('$toast_class'); // Add the toast class for color styling
                            var toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        });
                    </script>";
                }
            }
        }
    }

    // Display toast message dynamically
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('liveToast');
            var toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = '$message';
            toastEl.classList.add('$toast_class'); // Add the toast class for color styling
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    </script>";
}
?>
