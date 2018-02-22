<?php

	error_reporting(E_ALL);
	
	require_once('getProp.php');
	require_once('controller/db.php');
	require_once('controller/EntityManager.php');

	//Load properties file
	$properties = include 'getProp.php';

	$admin 		= $properties->{'vtiger_login'};
	$access_key = $properties->{'vtiger_ac'};

	//Make sure that it is a POST request.
	if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
	    throw new Exception('Request method must be POST!');
	}
	 
	//Make sure that the content type of the POST request has been set to application/json
	$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
	if(strcasecmp($contentType, 'application/json') != 0) {
	    throw new Exception('Content type must be: application/json');
	}
	 
	//Receive the RAW post data.
	$content = trim(file_get_contents("php://input"));

	$uuid 		= 	"";
	$timestamp 	=	"";
	$number		= 	"";

	//Parse json
	$obj = json_decode($content);
	if ($obj != NULL) {
		if (isset($obj->{"uuid"})) 			$uuid		=	$obj->{"uuid"}; 		else die("UUID is missing!");
		if (isset($obj->{"timestamp"})) 	$timestamp	=	$obj->{"timestamp"}; 	else die("timestamp is missing!");
		if (isset($obj->{"number"})) 		$number		=	$obj->{"number"}; 		else die("number is missing!");
	}
	else {
		die("Data sent is not a valid json object!<br>");
	}


	//Create EntityManager to operate VTiger
	$entityManager = new EntityManager($properties->{'vtiger_url'});

	//Create DB manager to operate freeswitch DB
	$DB = new DB($properties);

	///Find call by uuid in VTiger and set Status 'ringing'\
	//prepare callback data
	$status = 200;
	$message = "OK";
	//Perform login into VTiger
	$login = $entityManager->WSClient->doLogin($admin, $access_key);
	if (!$login) {
		echo "Login failed!\n";
		$status = 401;
		$message = "Bad VTiger credentials, check properties.json";
	}
	else {
		//Find call by uuid
		$sql = "SELECT * FROM " . $entityManager->callsModule . " WHERE uuid = '" . $uuid . "';";
		$records = $entityManager->WSClient->doQuery($sql);

		// var_dump($records);

		if($records) {
			$columns = $entityManager->WSClient->getResultColumns($records);
			
			$pos = strpos($records[0]["id"], "x") + 1;
			$id = substr($records[0]["id"], $pos);

			// echo "Id is " . $id . "<br>";

			$conn = new mysqli($properties->{'db_server'}, $properties->{'vtiger_db_user'}, $properties->{'vtiger_db_pass'}, $properties->{'vtiger_db'});

			$sql = "UPDATE vtiger_calls SET `status` = 'ringing' WHERE callsid = " . $id . ";";
			// echo $sql;
			
			if ($conn->query($sql) === true) {
				echo "Call status updated!";

				$sql = "UPDATE calls SET answered = 0 WHERE uuid LIKE '" . $uuid . "';";
				$DB->connection->query($sql);

			}
			else {
				echo "Error: " . $sql . "<br>" . $conn->error;
				$status = 422;
				$message = "Error updating call status";
			}
			$conn->close();
		}
		else {
			$status = 404;
			$message = "No record with uuid = $uuid found";
		}
	}

	$response = Array(
		"status"	=>	$status,
		"message"	=>	$message
	);

	$response_json = json_encode($response);
	
	echo $response_json;

?>