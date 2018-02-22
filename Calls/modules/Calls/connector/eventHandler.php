<?php

	error_reporting(E_ALL);

	header('Content-Type: text/event-stream');
	// header("Content-Type: application/json");
	
	require_once('getProp.php');
	require_once('controller/db.php');
	require_once('controller/EntityManager.php');

	//Load properties file
	$properties = include 'getProp.php';

	$id = -1;
	$uuid = -1;
	$number_from = -1;
	$number_to = -1;
	$call_event = -1;
	$start_time = -1;

	$data = Array(
		"id"			=>	$id,
		"uuid"			=>	$uuid,
		"number_from"	=>	$number_from,
		"number_to"		=>	$number_to,
		"direction"		=> 	$call_event,
		"start_time"	=>	$start_time
	);
	$calls = array();
	$conn = new mysqli($properties->{'db_server'}, $properties->{'db_user'}, $properties->{'db_password'}, $properties->{'db_name'});
	if ($conn->connect_error) {
		echo "Db error";
	}
	else {
		$result = getUnansweredCalls($conn);
		
		if ($result->num_rows > 0) {
			
			while($row = $result->fetch_assoc()) {
				$id = $row["id"];
				$uuid = $row["uuid"];
				$number_from = $row["number_from"];
				$number_to = $row["number_to"];
				$call_event = $row["call_event"];
				$start_time = $row["start_time"];

				$data = Array(
					"id"			=>	$id,
					"uuid"			=>	$uuid,
					"number_from"	=>	$number_from,
					"number_to"		=>	$number_to,
					"direction"		=>	$call_event,
					"start_time"	=>	$start_time
				);
				
				array_push($calls, $data);
				
			}
		}
	}
	
	$response = json_encode($calls);

	echo "$response";
	flush();

	function getUnansweredCalls($conn) {
		$sql = "SELECT * FROM calls WHERE answered = 0 ORDER BY start_time;";
		return $conn->query($sql);
	}