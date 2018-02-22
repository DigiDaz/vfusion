<?php

	error_reporting(E_ALL);

	if (!empty($_POST)) {
		$number_to = $_POST['number'];
		$user_id = $_POST['user_id'];
		$number_from = "unknown";

		$properties = include 'getProp.php';

		$conn = new mysqli($properties->{'db_server'}, $properties->{'vtiger_db_user'}, $properties->{'vtiger_db_pass'}, $properties->{'vtiger_db'});

		$sql = "select phone_crm_extension from vtiger_users where id = " . $user_id . ";";

		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$number_from = $row["phone_crm_extension"];
			}
		}

		$fusion_api_key = $properties->{'fusion_api_key'};
		$fusion_url = $properties->{'fusion_url'};

		$response = Array(
			'src' 	=> 	$number_from,
			'dest'	=>	$number_to,
			'key'	=>	$fusion_api_key,
			'url'	=>	$fusion_url
		);

		$response_json = json_encode($response);

		echo $response_json;

		$conn->close();
	}
?>