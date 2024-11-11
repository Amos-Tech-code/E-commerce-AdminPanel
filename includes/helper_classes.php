<?php
class Result {
    private $isError;
    private $message;

    function isError() { 
        return $this->isError;
    }

    function setErrorStatus($errorStatus) {
        $this->isError = $errorStatus;
    }

    function getMessage() {
        return $this->message;
    }

    function setMessage($message) {
        $this->message = $message;
    }
}
?>
