<!-- connect file -->
<?php
include('../includes/connect.php');
include('../functions/common_functions.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome <?php echo $_SESSION['username'] ?></title>
    <!-- bootstrap css link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <!-- css file -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
  <!-- navbar -->
  <div class="container-fluid p-0">
    <!-- first child-->
    <nav class="navbar navbar-expand-lg bg-info">
  <div class="container-fluid">
    <img src="../images/logo.png" alt="" class="logo">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="../index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../display_all.php">products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">My Account</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../cart.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><sup><?php cart_item(); ?></sup></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">total price: <?php total_cart_price(); ?>\-</a>
        </li>
        </ul>
      <form class="d-flex" role="search" action="../search_product.php" method="get">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search_data_product">
        <input type="submit" value="search" class="btn btn-outline-light" name="search_data_product">
      </form>
    </div>
  </div>
</nav>

<!-- calling cart function -->
<?php
cart();
?>

<!-- second child -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
   <ul class="navbar-nav me-auto">
  
        <?php
        if(!isset($_SESSION['username'])){
          echo "<li class='nav-item'>
          <a class='nav-link' href='#'>Welcome Guest</a>
        </li>";
        }else{
          echo "<li class='nav-item'>
          <a class='nav-link' href='#'>Welcome ".$_SESSION['username']."</a>
        </li>";
        }

if(!isset($_SESSION['username'])){
  echo "<li class='nav-item'>
  <a class='nav-link' href='./users_area/user_login.php'>login</a>
</li>";
}else{
  echo "<li class='nav-item'>
  <a class='nav-link' href='user_logout.php'>logout</a>
</li>";
}
?>
        
   </ul> 
 </nav>

 <!-- third child -->
    <div class="bg-light">
        <h3 class="text-center">Hidden store</h3>
        <p class="text-center">communications is at the heart of e-comerce and community</p>
    </div>

<!-- fourth child -->
<div class="row">
    <div class="col-md-2 p-0">
        <ul class="navbar-nav bg-secondary text-center" style="height:100vh">
            <li class="nav-item bg-info">
                <a href="" class="nav-link text-light"><h4>Your Profile</h4></a>
            </li>

            <?php
$username=$_SESSION['username'];
$user_image="select * from user_table where username='$username'";
$user_image=mysqli_query($con,$user_image);
$row_image=mysqli_fetch_array($user_image);
//$user_image = $row_image['user_image'] ?? ''; // If user_image is set, use its value, otherwise set it to an empty string
// Check if user_image is set and not empty
if(isset($row_image['user_image']) && !empty($row_image['user_image'])) {
  // If user_image is set and not empty, use its value
  $user_image = $row_image['user_image'];
  echo "<li class='nav-item'>
<img src='./user_images/$user_image' class='profile_img my-4' alt=''>
</li>";
} else {
  // If user_image is not set or empty, use the first letter of the username
  $user_image = substr($username, 0, 1); // Get the first character of the username
  $user_image = strtoupper($user_image); // Convert to uppercase (optional)
}


//echo "<li class='nav-item'>
//<img src='./user_images/$user_image' class='profile_img my-4' alt=''>
//</li>";

            ?>
            
            <li class="nav-item">
                <a href="profile.php" class="nav-link text-light">Pending Orders</a>
            </li>
            <li class="nav-item">
                <a href="profile.php?edit_account" class="nav-link text-light">Edit Account</a>
            </li>
            <li class="nav-item">
                <a href="profile.php?my_orders" class="nav-link text-light">My Orders</a>
            </li>
            <li class="nav-item">
                <a href="profile.php?delete_account" class="nav-link text-light">Delete Account</a>
            </li>
            <li class="nav-item">
                <a href="user_logout.php" class="nav-link text-light">Logout</a>
            </li>
        </ul>
    </div>
    <div class="col-md-10">
        <?php
    get_user_order_details();
    if(isset($_GET['edit_account'])){
      include('edit_account.php');
    }
    if(isset($_GET['my_orders'])){
      include('user_orders.php');
    }
    if(isset($_GET['delete_account'])){
      include('delete_account.php');
    }
    ?>
    </div>
</div>


<!-- last child -->
    <!-- include footer -->
    <?php include("../includes/footer.php")?>
  </div> 




<!-- bootstrap js link -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>