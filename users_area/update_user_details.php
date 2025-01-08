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
    if (!empty($data->user_id) && !empty($data->new_name) && !empty($data->new_email) && !empty($data->password)) {
        $user_id = $data->user_id;
        $new_name = $data->new_name;
        $new_email = $data->new_email;
        $password = $data->password;

        // Optional fields for password update
        $update_password = !empty($data->new_password) ? $data->new_password : null;

        // Verify user and current password
        $stmt = $con->prepare("SELECT user_email, user_password FROM user_table WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($current_email, $hashed_password);
            $stmt->fetch();

            // Verify the current password
            if (password_verify($password, $hashed_password)) {
                $stmt->close();

                // Check if new email exists for another user
                $stmt = $con->prepare("SELECT user_id FROM user_table WHERE user_email=? AND user_id!=?");
                $stmt->bind_param("si", $new_email, $user_id);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $result->setErrorStatus(true);
                    $result->setMessage("The provided email address is already in use by another user.");
                } else {
                    $stmt->close();

                    // Start building the update query
                    $update_query = "UPDATE user_table SET username=?, user_email=?";
                    $params = [$new_name, $new_email];
                    $types = "ss";

                    // Check if new password is provided
                    if ($update_password) {
                        $hashed_new_password = password_hash($update_password, PASSWORD_BCRYPT);
                        $update_query .= ", user_password=?";
                        $params[] = $hashed_new_password;
                        $types .= "s";
                    }

                    $update_query .= " WHERE user_id=?";
                    $params[] = $user_id;
                    $types .= "i";

                    // Execute the update query
                    $stmt = $con->prepare($update_query);
                    $stmt->bind_param($types, ...$params);

                    if ($stmt->execute()) {
                        $result->setErrorStatus(false);
                        $result->setMessage($update_password ? "User details and password updated successfully." : "User details updated successfully.");
                    } else {
                        $result->setErrorStatus(true);
                        $result->setMessage("Failed to update user details. Please try again.");
                    }
                }
            } else {
                $result->setErrorStatus(true);
                $result->setMessage("Invalid current password.");
            }
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("User not found.");
        }
        $stmt->close();
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
