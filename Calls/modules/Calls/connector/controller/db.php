<?php
require_once 'model/Call.php';
/**
* 
*/
class DB
{
	var $connection;
	
	function __construct($properties)
	{
		//Create connection
		$this->connection = new mysqli($properties->{'db_server'}, $properties->{'db_user'}, $properties->{'db_password'}, $properties->{'db_name'});
		//Check connection
		if ($this->connection->connect_error) {
			die ("Connection to DB failed: " . $this->connection->connect_error);
		}
	}

	function writeCall(Call $call) {
		// echo $call->uuid . "\n";
		// echo $call->start_time . "\n";
		// echo $call->call_event . "\n";
		// echo $call->src->number . "\n";

		$sql = "INSERT INTO calls (uuid, start_time, call_event, number_from, number_to) VALUES('" . $call->uuid . "', '" . $call->start_time . "', '" . $call->call_type . "', '" . $call->src->number . "', '". $call->dst ."');";
		// echo $sql . "\n";
		if ($this->connection->query($sql) === true) {
			return true;
		}
		else {
			echo $this->connection->error;
			return false;
		}
	}

	function query($sql) {
		if ($this->connection->query($sql) === true) {
			return true;
		}
		else {
			echo $this->connection->error;
			return false;
		}
	}
}