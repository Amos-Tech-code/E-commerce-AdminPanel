<?php
include('../includes/connect.php');

// Query to fetch the current values from the database
$query = "SELECT * FROM ordercharges WHERE id = 1";
$result = mysqli_query($con, $query);

// Check if data exists
$orderCharges = mysqli_fetch_assoc($result);
if (!$orderCharges) {
    $orderCharges = ['tax' => '0.00', 'shipping' => '0.00', 'discount' => '0.00']; // Default values
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Manage Order Charges & Admins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5 pt-4">
    <!-- Order Charges Settings -->
    <h3 class="text-center text-primary mb-4">Order Charges Settings</h3>
    <form action="save_settings.php" method="POST" class="shadow p-4 rounded bg-light mb-5">
        <div class="row mb-3">
            <label for="tax" class="col-sm-2 col-form-label">Tax</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="tax" name="tax" step="0.01" min="0" value="<?php echo htmlspecialchars($orderCharges['tax']); ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="shipping" class="col-sm-2 col-form-label">Shipping Charge</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="shipping" name="shipping" step="0.01" min="0" value="<?php echo htmlspecialchars($orderCharges['shipping']); ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="discount" class="col-sm-2 col-form-label">Discount</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="discount" name="discount" step="0.01" min="0" value="<?php echo htmlspecialchars($orderCharges['discount']); ?>" required>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> Save Changes</button>
        </div>
    </form>

    <!-- Admin Registration Section -->
        <h3 class="text-center text-primary mb-4">Register New Admin</h3>
        <form method="POST" action="save_admin.php" class="shadow p-4 rounded bg-light">
            <div class="row mb-3">
                <label for="username" class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" name="admin_username" id="username" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="admin_email" id="email" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="password" class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" name="admin_password" id="password" class="form-control" required>
                </div>
            </div>
            <div class="row mb-3">
        <label for="status" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select name="admin_status" id="status" class="form-select" required>
                <option value="" disabled selected>Select Status</option>
                <option value="minor">Minor</option>
                <option value="major">Major</option>
            </select>
        </div>
        </div>
            <div class="text-center">
                <button type="submit" name="register_admin" class="btn btn-primary btn-lg"><i class="fas fa-user-plus"></i> Register Admin</button>
            </div>
        </form>
</div>

<!-- Toast Notification -->
<?php if (isset($_GET['status'])): ?>
    <div class="toast-container position-fixed top-50 start-50 translate-middle">
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="toast align-items-center text-bg-success" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        Settings updated successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php elseif ($_GET['status'] == 'admin_added'): ?>
            <div class="toast align-items-center text-bg-success" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        New admin added successfully!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php elseif ($_GET['status'] == 'admin_minor'): ?>
            <div class="toast align-items-center text-bg-danger" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        New admin can only be added by the Major admin account!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php elseif ($_GET['status'] == 'email_exists'): ?>
            <div class="toast align-items-center text-bg-danger" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        Failed. The email you entered is already used!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>        
            <?php elseif ($_GET['status'] == 'form_incomplete'): ?>
            <div class="toast align-items-center text-bg-danger" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                    Failed to complete the operation. Incomplete form!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>    
        <?php elseif ($_GET['status'] == 'error'): ?>
            <div class="toast align-items-center text-bg-danger" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        Failed to complete the operation. Please try again.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastElement = document.querySelector('.toast');
        if (toastElement) {
            var toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    });
</script>

</body>
</html>
