<?php
	error_reporting(E_ALL);

	require_once 'model/Contact.php';
	require_once 'controller/EntityManager.php';

	if (!empty($_POST)) {

		$firstname = $_POST["firstname"];
		$lastname = $_POST["lastname"];
		$department = $_POST["department"];
		$mobile = $_POST["mobile"];
		$email = $_POST["email"];

		$properties = include 'getProp.php';
		$conn = new mysqli($properties->{'db_server'}, $properties->{'db_user'}, $properties->{'db_password'}, $properties->{'db_name'});

		$entityManager = new EntityManager($properties->{'vtiger_url'});

		$admin 		= $properties->{'vtiger_login'};
		$access_key = $properties->{'vtiger_ac'};

		$login = $entityManager->WSClient->doLogin($admin, $access_key);
		if ($login) {
			$contact = new Contact();
			$contact->firstname = $firstname;
			$contact->lastname = $lastname;
			$contact->department = $department;
			$contact->mobile = $mobile;
			$contact->email = $email;

			$data = $contact->toArray();

			$contact_id = $entityManager->addContact($data);
			if ($contact_id != -1) {

				$sql = "UPDATE calls SET answered = 1 WHERE number_from = '" . $mobile . "';";
				$result = $conn->query($sql);
				// echo "mobile is " . $mobile . "<br>";

				if ($result) {
					$conn = new mysqli($properties->{'db_server'}, $properties->{'vtiger_db_user'}, $properties->{'vtiger_db_pass'}, $properties->{'vtiger_db'});
					$sql = "UPDATE vtiger_calls	 SET callerid = " . $contact_id . " WHERE caller_phone = '" . $mobile . "';";
	
					$result = $conn->query($sql);
	
					if ($result)
						echo '{"data": "'. $contact_id .'"}';
					else 
						echo '{"error": "error updating `answered` status"}';
				}
				else {
					var_dump($result);
				}

				
			}
			else {
				echo '{"error": "error adding record into VTiger"}';
			}

		}
		else {
			echo '{"error": "error loggin into VTiger, plese check your access key in the connector preferences"}';
		}
	}
	else {
		echo '{"error": "POST is empty!"}';
	}