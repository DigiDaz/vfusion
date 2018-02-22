<?php

include_once('vtwsclib/Vtiger/WSClient.php');
require_once('model/Call.php');
error_reporting(E_ALL);
/**
* 
*/
class EntityManager
{
	
	var $contactModule = 'Contacts';
	var $callsModule = 'Calls';
	var $url = '';

	var $WSClient;


	function __construct($url) {
		$this->url = $url;
		$this->WSClient = new Vtiger_WSClient($url);
	}

	function addCall($call) {
		$record = $this->WSClient->doCreate($this->callsModule, $call);
		if ($record) {
			$recordid = $this->WSClient->getRecordId($record['id']);
			return $recordid;
		}
		else return -1;
	}

	function addContact($contact) {

		$record = $this->WSClient->doCreate($this->contactModule, $contact);
		if ($record) {
			$recordid = $this->WSClient->getRecordId($record['id']);
			return $recordid;
		}
		else return -1;

	}

	function findContact($id) {
		return $this->doRetrieve($id);
	}

	function doRetrieve($record_id) {
		$record = $this->WSClient->doRetrieve($record_id);
		if ($record) {
			return $record;
		}
		else {
			return -1;
		}
	}
}

?>