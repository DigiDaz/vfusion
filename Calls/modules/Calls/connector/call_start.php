<?php

	error_reporting(E_ALL);
	
	require_once 'getProp.php';
	require_once 'model/Call.php';
	require_once 'controller/db.php';
	require_once 'controller/EntityManager.php';

	//Simple class for response data
	class ContactData {
		var $name = '';
		var $company = '';
		var $manager = '';

		function __construct($name, $company, $manager) {
			$this->name = $name;
			$this->company = $company;
			$this->manager = $manager;
		}
	}

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
	$src		=	"";			
	$dst		=	"";

	if ($obj != NULL) {
		if (isset($obj->{'uuid'})) 		$uuid 		= $obj->{'uuid'}; 		else die("UUID is missing!");
		if (isset($obj->{'timestamp'})) $timestamp 	= $obj->{'timestamp'}; 	else die("timestamp is missing!");
		if (isset($obj->{'direction'})) $direction 	= $obj->{'direction'};	else die("direction is missing!");
		if (isset($obj->{'src'})) 		$src 		= $obj->{'src'};		else die("src is missing!");
		if (isset($obj->{'dst'})) 		$dst 		= $obj->{'dst'};		else die("dst is missing!");
	}
	else {
		die("sent data is not a valid json object!<br>");
	}



	///Create record in connector's DB
	
	//Load properties file
	$properties = include 'getProp.php';

	$admin 		= $properties->{'vtiger_login'};
	$access_key = $properties->{'vtiger_ac'};
	
	//Create DB instance
	$DB = new DB($properties);

	//Create model representation of Call
	$call = new Call();
	$call->uuid = $uuid;
	$call->start_time = gmdate("Y-m-d G:i:s", $timestamp);
	$call->call_type = $direction;
	$call->src = $src;
	$call->dst = $dst;

	//Create EntityManager to manage VTiger entities
	$entityManager = new EntityManager($properties->{'vtiger_url'});

	$status = 200;
	$message = "OK";
	$data = null;

	//Perform login into VTiger
	$login = $entityManager->WSClient->doLogin($admin, $access_key);
	//Save our call instance in connector's DB
	if ($login) {
		// echo "New call is inserted!\n";
		///In case of success create appropriate record in VTiger
		
		if ($DB->writeCall($call) !== true) {
			//Error connecting to Db
			echo "Error inserting call into connector's DB\n";
			$status = 422;
			$message = "Error inserting call into connector's DB";
		}
		else {

			//check UUID is unique
			$records = $entityManager->WSClient->doQuery("SELECT uuid FROM Calls WHERE uuid = '" . $uuid . "';");
			if ($records) {
				//duplicate found!
				$status = 401;
				$message = "Object with this UUID already exists";
			}
			else {
					// echo "Logged in!\n";
				$record_id = $entityManager->addCall( Array(
					'uuid'			=>	$uuid,
					'CallStart'		=>	gmdate("Y-m-d G:i:s", $timestamp),
					'direction'		=>	$direction,
					'CallerPhone'	=>	$src->number,
					// 'Caller'		=>	$src->name
				));
				//Error
				if ($record_id == -1) {
					echo "Error creating new VTiger entity\n";
					// die("Error creating new VTiger entity");
					$status = 422;
					$message = "Error creating new VTiger entity";
				}
				else {
				//Check if user with number is present int VTiger
				$sql = "SELECT * FROM " . $entityManager->contactModule . " WHERE mobile = '" . $src->number . "' OR mobile = '+". $src->number ."' OR phone = '". $src->number ."' OR phone = '+". $src->number ."';";
				$records = $entityManager->WSClient->doQuery($sql);

					if ($records) {
						// echo "Found related Contact\n";
					
						$conn = new mysqli($properties->{'db_server'}, $properties->{'vtiger_db_user'}, $properties->{'vtiger_db_pass'}, $properties->{'vtiger_db'});

						$pos = strpos($records[0]["id"], "x") + 1;

						$sql = "UPDATE vtiger_calls SET callerid = " . substr($records[0]["id"], $pos) . " WHERE callsid = " . $record_id;
					
						if ($conn->query($sql) === true) {
							// echo "Linked call entity to Contact\n";
							$data = new ContactData($records[0]["firstname"] . " " . $records[0]["lastname"], $records[0]["department"], $records[0]["mobile"]);
						}
						else {
							echo "Error: " . $sql . "<br>" . $conn->error;
							$status = 422;
							$message = "Error linking call entity to Contact\n";
						}

						$conn->close();
					}
				}
			}
		}

		
	}
	else {
		echo "Login failed!\n";
		$status = 401;
		$message = "Bad VTiger credentials, check properties.json";
	}

	$response = array(
		"status"	=>	$status,
		"message"	=>	$message,
		"data"		=> 	$data
	);

	$response_json = json_encode($response);

	// header('Content-Type: application/json');
	echo $response_json;

?>