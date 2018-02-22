<?php

error_reporting(E_ALL);

if (isset($_POST)) {

    $properties = include 'getProp.php';

    $properties->{'vtiger_login'} = $_POST["vtiger_login"];
    $properties->{'vtiger_ac'} = $_POST["vtiger_ac"];
    $properties->{'fusion_api_key'} = $_POST["fusionApi"];
    $properties->{'fusion_url'} = $_POST["fusionUrl"];
    
    $fields = array();

    if (isset($_POST["firstname"]))     array_push($fields, "firstname");
    if (isset($_POST["lastname"]))      array_push($fields, "lastname");
    if (isset($_POST["email"]))         array_push($fields, "email");
    if (isset($_POST["company"]))       array_push($fields, "company");
    if (isset($_POST["department"]))    array_push($fields, "department");
    if (isset($_POST["phone"]))         array_push($fields, "phone");

    $properties->{'shownFields'} = $fields;

    $json = json_encode($properties);

    $prop_file = fopen("../../../properties.json", "w");
	fwrite($prop_file, $json);
	fclose($prop_file);

    header("Location: ". $properties->{'vtiger_url'} ."index.php?module=Calls&view=List&app=PROJECT");
}
else {
    die("Invalid request!");
}