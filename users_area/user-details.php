<?php
require_once("../includes/connect.php");
require_once("../includes/helper_classes.php");

//Array for response data
$response = array();
$result = new Result();

if (!empty($_POST['email']) && !empty($_POST['token'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];

    $stmt = $con->prepare("SELECT user_password FROM user_table WHERE user_email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($dbPass);
    $stmt->fetch();
    $rows = $stmt->num_rows;
    $stmt->close();
    if($rows>0) {
        $stmt = $con->prepare("SELECT token FROM user_table WHERE user_email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($savedToken);
        $stmt->fetch();
        $stmt->close();
        if ($savedToken == $token) {
            $stmt = $con->prepare("SELECT username FROM user_table WHERE user_email=?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($name);
            $stmt->fetch();
            $stmt->close();

            $result->setErrorStatus(false);
            $result->setMessage("Retrieval Successful");
            $response['user']['name'] = $name;
        } else {
            $result->setErrorStatus(true);
            $result->setMessage("Invalid token");
        }
    } else {
        $result->setErrorStatus(true);
        $result->setMessage("Invalid credentials");
    }
} else {
    $result->setErrorStatus(true);
    $result->setMessage("insufficient parameters");
}

$response['result']['error'] = $result->isError();
$response['result']['message'] = $result->getMessage();

echo json_encode($response);
?>