<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

// Array for response data
$response = array();
$result = new Result();

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to retrieve user_password and user_id
    $stmt = $con->prepare("SELECT user_password, user_id FROM user_table WHERE user_email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($dbPass, $user_id);
    $stmt->fetch();
    $rows = $stmt->num_rows;
    $stmt->close();

    if ($rows > 0) {
        // User exists
        if (password_verify($password, $dbPass)) {
            $stmt = $con->prepare("SELECT username FROM user_table WHERE user_email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($name);
            $stmt->fetch();
            $stmt->close();

            // Generate a token
            $authToken = hash('sha384', microtime() . uniqid() . bin2hex(random_bytes(10)));

            // Update the token in the database
            $stmt = $con->prepare("UPDATE user_table SET token=? WHERE user_email=?");
            $stmt->bind_param("ss", $authToken, $email);
            $stmt->execute();

            // Include user_id in the response
            $response['user']['user_id'] = $user_id;
            $response['user']['name'] = $name;
            $response['user']['email'] = $email;
            $response['user']['auth_token'] = $authToken;

            $result->setErrorStatus(false);
            $result->setMessage("Login was successful.");
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("The password you entered is incorrect. Please try again.");
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("No account found with the email address $email. Please check your email or register.");
    }
} else {
    $missingParams = [];
    if (empty($_POST['email'])) $missingParams[] = "email";
    if (empty($_POST['password'])) $missingParams[] = "password";
    $missingFields = implode(" and ", $missingParams);
    $result->setErrorStatus(true);
    $result->setMessage("Please provide your $missingFields to proceed.");
}

$response['result']['error'] = $result->isError();
$response['result']['message'] = $result->getMessage();

echo json_encode($response);
?>
