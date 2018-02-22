<?php

	error_reporting(E_ALL);

	if (!empty($_POST["uuid"])) {
		$uuid = $_POST["uuid"];

		$properties = include 'getProp.php';

		$conn = new mysqli($properties->{'db_server'}, $properties->{'db_user'}, $properties->{'db_password'}, $properties->{'db_name'});

		$sql = "UPDATE calls SET answered = 1 WHERE uuid = '$uuid';";

		if ($conn->query($sql) === TRUE)
			echo "success $uuid";
		else 
			echo "error updating 'answered' status";
	}
	else {
		echo "Empty data sent!";
	}