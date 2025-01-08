<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);

$response = array();
$result = new Result();

if ($data != null) {
    if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
        $name = $data->name;
        $email = $data->email;
        $password = $data->password;

        // Check if the email already exists
        $stmt = $con->prepare("SELECT * FROM user_table WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $rows = $stmt->num_rows;
        $stmt->close();

        if ($rows == 0) {
            // Register new user
            $passEnc = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("INSERT INTO user_table(username, user_email, user_password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $passEnc);

            if ($stmt->execute()) {
                $result->setErrorStatus(false);
                $result->setMessage("Registration successful! You can now log in with your credentials.");
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("An unexpected error occurred while processing your registration. Please try again.");
            }
            $stmt->close();
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("The email address $email is already registered. Please use a different email.");
        }
    } else {
        // Identify missing parameters
        $missingParams = [];
        if (empty($data->name)) $missingParams[] = "name";
        if (empty($data->email)) $missingParams[] = "email";
        if (empty($data->password)) $missingParams[] = "password";
        $missingFields = implode(", ", $missingParams);

        $result->setErrorStatus(true);
        $result->setMessage("Please provide the following information to register: $missingFields.");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("No data received. Ensure all fields are filled and try again.");
}

$response['error'] = $result->isError();
$response['message'] = $result->getMessage();

echo json_encode($response);
?>
