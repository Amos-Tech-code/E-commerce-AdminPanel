<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form values
    $tax = mysqli_real_escape_string($con, $_POST['tax']);
    $shipping = mysqli_real_escape_string($con, $_POST['shipping']);
    $discount = mysqli_real_escape_string($con, $_POST['discount']);

    // SQL query to update the ordercharges table
    $query = "UPDATE ordercharges SET tax = '$tax', shipping = '$shipping', discount = '$discount' WHERE id = 1";
    $result = mysqli_query($con, $query);

    // Clear output buffer to prevent parent file interference
    ob_start();

    // Check if the query was successful
    if ($result) {
        // Redirect with success status
        header("Location: dashboard.php?settings_page&status=success");
        exit; // Ensure script stops executing here
    } else {
        // Redirect with failure status
        header("Location: dashboard.php?settings_page&status=error");
        exit; // Ensure script stops executing here
    }
}
?>
