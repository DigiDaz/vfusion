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
	if(strcasecmp($contentType, 'application/json') != 0){
	    throw new Exception('Content type must be: application/json');
	}
	 
	//Receive the RAW post data.
	$content = trim(file_get_contents("php://input"));

	//Parse json
	$obj = json_decode($content);

	$uuid 		= 	"";
	$timestamp 	=	"";
	$direction	= 	"";
	$callstatus	= 	"";
	$time		= 	"";
	$src		= 	"";
	$recording	= 	"";
	$last_seen	= 	"";

	if ($obj != NULL) {
		if (isset($obj->{"uuid"})) 			$uuid		=	$obj->{"uuid"}; 		else die("UUID is missing!");
		if (isset($obj->{"timestamp"})) 	$timestamp	=	$obj->{"timestamp"}; 	else die("timestamp is missing!");
		if (isset($obj->{"direction"})) 	$direction	=	$obj->{"direction"}; 	else die("direction is missing!");
		if (isset($obj->{"status"}))		$callstatus	=	$obj->{"status"}; 		else die("status is missing!");
		if (isset($obj->{"time"}))			$time		=	$obj->{"time"}; 		else die("time is missing!");
		if (isset($obj->{"src"}))			$src		=	$obj->{"src"}; 			else die("src is missing!");

		//Check optional parameters
		if (isset($obj->{"recording"})) $recording	=	$obj->{"recording"};
		if (isset($obj->{"last_seen"})) $last_seen	=	$obj->{"last_seen"};
	}
	else {
		die("Data sent is not a valid json object!");
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
	
		$sql = "SELECT * FROM " . $entityManager->callsModule . " WHERE uuid LIKE '" . $uuid . "';";
		$records = $entityManager->WSClient->doQuery($sql);

		if($records) {
			$columns = $entityManager->WSClient->getResultColumns($records);
			
			$pos = strpos($records[0]["id"], "x") + 1;
			$id = substr($records[0]["id"], $pos);

			$conn = new mysqli($properties->{'db_server'}, $properties->{'vtiger_db_user'}, $properties->{'vtiger_db_pass'}, $properties->{'vtiger_db'});
			$sql = "UPDATE vtiger_calls SET `status` = '" . $callstatus . "', direction = '" . $direction . "', duration_time =  '" . $time->duration . "', answered_time = '" . $time->answered . "', recording_url = '" . $recording . "' WHERE callsid = " . $id . ";";

			if ($conn->query($sql) === true) {

			}
			else {
				echo "Error: " . $sql . "<br>" . $conn->error;
				$status = 422;
				$message = "Error updating call status";
			}
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

	header("Content-type: application/json");
	echo $response_json;
