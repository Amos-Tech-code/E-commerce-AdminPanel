<?php

include('../includes/connect.php');
if(isset($_POST['insert_brand'])){
  $brand_title=$_POST['brand_title'];

  //select data from database
  $select_query="select *from brands where brand_title='$brand_title'";
  $result_select=mysqli_query($con,$select_query);
  $number=mysqli_num_rows($result_select);
  if($number>0){
    $toast_message = 'This is present inside the database';
    $toast_class = 'text-bg-danger';
  }
  else {
  $insert_query="insert into brands (brand_title) values ('$brand_title')";
  $result=mysqli_query($con,$insert_query);
  if($result){
    $toast_message = 'Brand has been inserted successfully';
    $toast_class = 'text-bg-success';
  }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert brand</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f7f9fc;
            font-family: Arial, sans-serif;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h2 {
            font-weight: bold;
            color: #28a745;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #17a2b8;
            color: white;
            font-size: 18px;
        }

        .form-control {
            border-radius: 6px;
            padding: 15px;
            font-size: 16px;
        }

        .btn-submit {
            background-color: #17a2b8;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #138496;
        }

        .toast-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .toast-body {
            font-size: 16px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="text-center">Insert Brands</h2>
        <form action="" method="post">
            <!-- Brand Title -->
            <div class="input-group w-75 m-auto mb-3">
                <span class="input-group-text"><i class="fa-solid fa-receipt"></i></span>
                <input type="text" class="form-control" name="brand_title" placeholder="Insert Brand Title" required aria-label="Brand Title">
            </div>

            <!-- Submit Button -->
            <div class="input-group w-75 m-auto">
                <input type="submit" class="btn-submit" name="insert_brand" value="Insert Brand">
            </div>
        </form>

        <?php if (isset($toast_message)) { ?>
        <div class="toast-container">
            <div class="toast align-items-center <?= $toast_class ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $toast_message ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <script>
            var toast = new bootstrap.Toast(document.querySelector('.toast'));
            toast.show();
        </script>
        <?php } ?>
    </div>

</body>
</html>
