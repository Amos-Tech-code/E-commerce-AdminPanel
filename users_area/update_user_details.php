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
    if (!empty($data->user_id) && !empty($data->new_name) && !empty($data->new_email)) {
        $user_id = $data->user_id;
        $new_name = $data->new_name;
        $new_email = $data->new_email;

        // Prepare query to update user details
        $stmt = $con->prepare("SELECT * FROM user_table WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        $rows = $stmt->num_rows;
        $stmt->close();

        if ($rows > 0) {
            // Update user information
            $stmt = $con->prepare("UPDATE user_table SET username=?, user_email=? WHERE user_id=?");
            $stmt->bind_param("ssi", $new_name, $new_email, $user_id);

            if ($stmt->execute()) {
                $result->setErrorStatus(false);
                $result->setMessage("User details updated successfully.");
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("Something went wrong. Please try again.");
            }
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("User not found.");
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Insufficient parameters.");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("No data received.");
}

$response['error'] = $result->isError();
$response['message'] = $result->getMessage();
echo json_encode($response);
?>
