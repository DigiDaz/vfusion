<?php

//Read database credentials from properties file if it exists
	if (file_exists("../../../properties.json")) {
		// echo file_get_contents("properties")."<br>";
		$properties = json_decode(file_get_contents("../../../properties.json"));	

		return $properties;
	}
	//If not then die
	else {
		die("The connector properties file is missing!");
	}