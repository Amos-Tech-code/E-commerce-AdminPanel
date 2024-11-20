<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        h3 {
            margin-top: 40px;
            font-size: 2rem;
            font-weight: 600;
            color: #28a745;
        }

        .table-container {
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
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

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .modal-content {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .modal-header {
            background-color: #dc3545;
            color: white;
        }

        .modal-footer .btn {
            border-radius: 20px;
            font-weight: 600;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-close {
            color: #ffffff;
        }

        .icon-link {
            text-decoration: none;
            color: #dc3545;
        }

        .icon-link:hover {
            color: #28a745;
        }

        .icon-link i, .icon-link svg {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center">All Categories</h3>

    <!-- Table Container -->
    <div class="table-container">
        <table class="table table-bordered table-hover">
            <thead class="text-center">
                <tr>
                    <th>Serial No</th>
                    <th>Category Title</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_category = "select * from categories";
                $result = mysqli_query($con, $select_category);
                $number = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $category_id = $row['category_id'];
                    $category_title = $row['category_title'];
                    $number++;
                ?>
                    <tr class="text-center">
                        <td><?php echo $number; ?></td>
                        <td><?php echo $category_title; ?></td>
                        <td>
                           <!-- Edit Button with Bootstrap Classes -->
                           <a class="btn btn-info text-white" href="dashboard.php?edit_categories=<?php echo $category_id; ?>">
                                <i class="fas fa-pen-square"></i> Edit
                            </a>
                        </td>
                        <td>
                            <!-- Trigger modal for delete confirmation -->
                            <a class="icon-link" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" onclick="setDeleteUrl('dashboard.php?delete_category=<?php echo $category_id; ?>')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap Modal for Delete Confirmation -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this category?
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
