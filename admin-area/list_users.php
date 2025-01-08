<?php
include('../includes/connect.php');

// Fetch logged-in admin's status
$admin_id = $_SESSION['admin_id']; // Assuming admin_id is stored in the session
$admin_status_query = "SELECT status FROM admin_table WHERE admin_id = $admin_id";
$admin_status_result = mysqli_query($con, $admin_status_query);
$admin_status_row = mysqli_fetch_assoc($admin_status_result);
$admin_status = $admin_status_row['status'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1 {
            margin-top: 20px;
        }
        .table-container {
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #17a2b8;
            color: #ffffff;
        }
        .table tbody tr {
            transition: background-color 0.3s;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .modal-content {
            border-radius: 8px;
        }
        .btn-close {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-success">All Users</h1>

        <?php if ($admin_status === 'major') { ?>
            <!-- Tabs for Major Admin -->
            <ul class="nav nav-tabs" id="userTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="normal-users-tab" data-bs-toggle="tab" data-bs-target="#normal-users" type="button" role="tab" aria-controls="normal-users" aria-selected="true">Normal Users</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="admin-users-tab" data-bs-toggle="tab" data-bs-target="#admin-users" type="button" role="tab" aria-controls="admin-users" aria-selected="false">Admin Users</button>
                </li>
            </ul>
        <?php } ?>

        <div class="tab-content mt-4">
            <!-- Normal Users Table -->
            <div class="tab-pane fade show active" id="normal-users" role="tabpanel" aria-labelledby="normal-users-tab">
                <div class="table-container table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>User Id</th>
                                <th>Username</th>
                                <th>User Email</th>
                                <th>User Address</th>
                                <th>User Mobile</th>
                                <th>Delete User</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $get_users = "SELECT * FROM user_table";
                        $result = mysqli_query($con, $get_users);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $user_id = $row['user_id'] ?? 'NULL';
                            $username = $row['username'] ?? 'NULL';
                            $user_email = $row['user_email'] ?? 'NULL';
                            $user_address = $row['user_address'] ?? 'NULL';
                            $user_mobile = $row['user_mobile'] ?? 'NULL';
                        ?>
                            <tr class="text-center">
                                <td><?php echo $user_id; ?></td>
                                <td><?php echo $username; ?></td>
                                <td><?php echo $user_email; ?></td>
                                <td><?php echo $user_address; ?></td>
                                <td><?php echo $user_mobile; ?></td>
                                <td>
                                    <a class="text-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" onclick="setDeleteUrl('dashboard?delete_user=<?php echo $user_id; ?>')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($admin_status === 'major') { ?>
                <!-- Admin Users Table -->
                <div class="tab-pane fade" id="admin-users" role="tabpanel" aria-labelledby="admin-users-tab">
                    <div class="table-container table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="text-center">
                                <tr>
                                    <th>Admin Id</th>
                                    <th>Admin Username</th>
                                    <th>Admin Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $get_admins = "SELECT * FROM admin_table";
                            $result = mysqli_query($con, $get_admins);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $admin_id = $row['admin_id'] ?? 'NULL';
                                $admin_username = $row['admin_username'] ?? 'NULL';
                                $admin_email = $row['admin_email'] ?? 'NULL';
                                $status = $row['status'] ?? 'NULL';
                            ?>
                                <tr class="text-center">
                                    <td><?php echo $admin_id; ?></td>
                                    <td><?php echo $admin_username; ?></td>
                                    <td><?php echo $admin_email; ?></td>
                                    <td><?php echo $status; ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Modal for Deletion Confirmation -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                    <br><small class="text-muted">Deletion will not occur if there are associated orders.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setDeleteUrl(url) {
            document.getElementById('confirmDeleteBtn').setAttribute('href', url);
        }
    </script>
</body>
</html>
