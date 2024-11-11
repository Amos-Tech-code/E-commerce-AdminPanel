<?php
include('../includes/connect.php');

// Check if user is logged in as admin
if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit();
}

// Fetch all users from the user table
$query = "SELECT * FROM user_table";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Channels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5 pt-4">
    <h3 class="text-center text-success">Chat Channels</h3>
    <p class="text-center">Select a user to start chatting</p>

    <!-- List of Channels (Users) -->
    <div class="list-group">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <a href="dashboard.php?chat=<?php echo $row['user_id']; ?>" class="list-group-item list-group-item-action">
                <?php echo $row['username']; ?>
            </a>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
