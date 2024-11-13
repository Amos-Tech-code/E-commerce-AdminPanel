<?php
include('../includes/connect.php');
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

    <!-- Page Header -->
    <div class="container">
        <h1 class="text-center text-success">All Users</h1>

        <!-- User Table -->
        <div class="table-container mt-4">
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
                        global $con;
                        $get_users = "SELECT * FROM user_table";
                        $result = mysqli_query($con, $get_users);
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Check if each column exists in the row and set to NULL if it doesn't
                            $user_id = isset($row['user_id']) ? $row['user_id'] : 'NULL';
                            $username = isset($row['username']) ? $row['username'] : 'NULL';
                            $user_email = isset($row['user_email']) ? $row['user_email'] : 'NULL';
                            $user_address = isset($row['user_address']) ? $row['user_address'] : 'NULL';
                            $user_mobile = isset($row['user_mobile']) ? $row['user_mobile'] : 'NULL';
                        ?>
                            <tr class="text-center">
                                <td><?php echo $user_id; ?></td>
                                <td><?php echo $username; ?></td>
                                <td><?php echo $user_email; ?></td>
                                <td><?php echo $user_address; ?></td>
                                <td><?php echo $user_mobile; ?></td>
                                
                                <!-- Delete link triggers the modal -->
                                <td>
                                    <a class="text-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" onclick="setDeleteUrl('dashboard.php?delete_user=<?php echo $user_id; ?>')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                </tbody>
            </table>
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

    <!-- JavaScript to Set Delete URL in Modal -->
    <script>
        function setDeleteUrl(url) {
            document.getElementById('confirmDeleteBtn').setAttribute('href', url);
        }
    </script>

</body>
</html>
