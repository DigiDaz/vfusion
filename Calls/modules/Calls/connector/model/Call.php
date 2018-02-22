<?php

/**
* A model representaion of Call
*/
class Call
{
	//Unique identifier of call
	var $uuid;

	//Call source - number of caller
	var $src;

	//Number of respondent
	var $dst;

	//Call start time (stored sa formated string)
	var $start_time;

	//Call end time   (stored sa formated string)
	var $end_time;

	//Call event = (inbound/outbound/local)
	var $call_type;
	
	//Call type = (answered/no_answer/busy/failed)
	var $call_status;

	function __construct() {

	}

	function toCallArray() {
		return Array(
			"direction"			=>	$this->call_type,
			"CallStatus"		=>	$this->call_status,
			"CallStart"			=>	$this->start_time,
			"CallerPhone"		=>	$this->src->number,
			"uuid"				=>	$this->uuid,
			"ReplierPhone"		=> 	$this->dst,
		);
	}

}

?>