<?php
session_start(); // Start the session to access session variables
include('../includes/connect.php');

// Check if admin is logged in and has "major" status
if (!isset($_SESSION['admin_id'])) {
    // Redirect if admin is not logged in
    header("Location: dashboard.php?settings_page&status=error");
    exit;
}

$loggedInAdminId = $_SESSION['admin_id']; // Use session data for logged-in admin

// Query to get admin's status
$query = "SELECT status FROM admin_table WHERE admin_id = $loggedInAdminId";
$result = mysqli_query($con, $query);
$adminData = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Check if the logged-in admin has 'major' status
    if ($adminData['status'] !== 'major') {
        // Redirect with error if logged-in admin is not 'major'
        header("Location: dashboard.php?settings_page&status=admin_minor");
        exit;
    }
    // Check if the form fields are set
    if (isset($_POST['admin_username'], $_POST['admin_email'], $_POST['admin_password'], $_POST['admin_status'])) {
        // Retrieve and sanitize form values
        $adminUsername = mysqli_real_escape_string($con, $_POST['admin_username']);
        $adminEmail = mysqli_real_escape_string($con, $_POST['admin_email']);
        $adminPassword = mysqli_real_escape_string($con, $_POST['admin_password']);
        $adminStatus = mysqli_real_escape_string($con, $_POST['admin_status']);

        // Check if the admin email already exists in the database
        $checkQuery = "SELECT * FROM admin_table WHERE admin_email = '$adminEmail'";
        $checkResult = mysqli_query($con, $checkQuery);

        // If the email already exists, redirect with an error message
        if (mysqli_num_rows($checkResult) > 0) {
            header("Location: dashboard.php?settings_page&status=email_exists");
            exit; // Ensure the script stops here
        }

        // Hash the password for security
        $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

        // SQL query to insert the new admin
        $query = "INSERT INTO admin_table (admin_username, admin_email, admin_password, status) 
                  VALUES ('$adminUsername', '$adminEmail', '$hashedPassword', '$adminStatus')";

        // Execute the query
        $result = mysqli_query($con, $query);

        // Clear output buffer to prevent parent file interference
        ob_start();

        // Check if the query was successful
        if ($result) {
            // Redirect with success status
            header("Location: dashboard.php?settings_page&status=admin_added");
            exit; // Ensure script stops executing here
        } else {
            // Redirect with failure status
            header("Location: dashboard.php?settings_page&status=error");
            exit; // Ensure script stops executing here
        }
    } else {
        // Handle the case where form fields are not set or empty
        header("Location: dashboard.php?settings_page&status=form_incomplete");
        exit;
    }
}
?>
