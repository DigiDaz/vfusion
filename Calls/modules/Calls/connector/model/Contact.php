<?php

class Contact {

	var $firstname = "";
	var $lastname = "";
	var $mobile = "";
	var $phone = "";
	var $email = "";
	var $department = "";

	function __construct() {}

	function toJSON() {
		return json_encode($this->toArray());
	}

	function toArray() {
		return Array(
			"firstname"		=> 	$this->firstname,
			"lastname"		=> 	$this->lastname,
			"mobile"		=> 	$this->mobile,
			"phone"			=> 	$this->phone,
			"email"			=> 	$this->email,
			"department"	=>	$this->department
		);
	}
}

?>