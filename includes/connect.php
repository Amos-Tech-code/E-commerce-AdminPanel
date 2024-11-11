<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "shopper";
	
	// Create connection
	$con = new mysqli($servername, $username, $password, $dbname);
    if (!$con)
        echo "connection error: ". mysqli_connect_error();


