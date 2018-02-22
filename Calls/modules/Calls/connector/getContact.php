<?php

	require_once('controller/EntityManager.php');

	$properties = include 'getProp.php';
	$conn = new mysqli($properties->{'db_server'}, $properties->{'db_user'}, $properties->{'db_password'}, $properties->{'db_name'});

	$entityManager = new EntityManager($properties->{'vtiger_url'});

	$admin 		= $properties->{'vtiger_login'};
	$access_key = $properties->{'vtiger_ac'};

	$login = $entityManager->WSClient->doLogin($admin, $access_key);
	if ($login) {
		if (!empty($_GET["number_from"])) {
			$number = str_replace(" ", "", $_GET["number_from"]);
			$sql = "SELECT * FROM " . $entityManager->contactModule . " WHERE mobile = '$number' OR mobile = '+$number' OR phone = '$number' OR phone = '+$number';";
			$records = $entityManager->WSClient->doQuery($sql);

			// echo $sql;

			if ($records) {
				$columns = $entityManager->WSClient->getResultColumns($records);
			
				$firstname = $records[0]["firstname"];
				$lastname = $records[0]["lastname"];
				if (!empty($records[0]["mobile"])) $mobile = $records[0]["mobile"];
				if (!empty($records[0]["phone"])) $mobile = $records[0]["phone"];
				
				$email = $records[0]["email"];
				$title = $records[0]["title"];
				$department = $records[0]["department"];
				// $contactId = $records[0]["id"];

				$pos = strpos($records[0]["id"], "x");
				$contactId = substr($records[0]["id"], $pos+1);

				$json = Array(
					"contactid"	=>	$contactId,
					"error"		=>	"false",
					"firstname"	=> 	$firstname,
					"lastname"	=>	$lastname,
					"mobile"	=>	$mobile,
					"email"		=>	$email,
					"title"		=>	$title,
					"department"=>	$department,
				);

				echo json_encode($json);
				die();
			}
			else {
				echo '{"error" : "Error retirieving from vtiger - no such records!"}';
			}
		}
		else {
			echo '{"error" : "Error getting info"}';
		}
	}
	else {
		echo '{"error" : "Error logging into vtiger"}';
	}

	$conn->close();

?>