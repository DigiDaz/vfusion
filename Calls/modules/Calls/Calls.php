<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/Vtiger/CRMEntity.php';
include_once 'modules/Calls/connector/FusionLogger.php';

class Calls extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_calls';
	var $table_index= 'callsid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_callscf', 'callsid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_calls', 'vtiger_callscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_calls' => 'callsid',
		'vtiger_callscf'=>'callsid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Uuid' => Array('calls', 'uuid'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Uuid' => 'uuid',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'uuid';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Uuid' => Array('calls', 'uuid'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Uuid' => 'uuid',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('uuid');

	// For Alphabetical search
	var $def_basicsearch_col = 'uuid';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'uuid';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('uuid','assigned_user_id');

	var $default_order_by = 'uuid';
	var $default_sort_order='ASC';


	var $config;

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
		// $adb->setDebug(true);
 		if($eventType == 'module.postinstall') {
			// TODO Handle actions after this module is installed.
			$this->initWebServices();
			$this->installConnector();

		} else if($eventType == 'module.disabled') {
			// TODO Handle actions before this module is being uninstalled.
		} else if($eventType == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			// TODO Handle actions after this module is updated.
		}
	 }
	 
	function initWebServices() {
		$moduleInstance = Vtiger_Module::getInstance('Calls');
		$moduleInstance->initWebservice();
	}
	
	function installConnector() {

		$this->config = $this->prepareData();

		// $logger = FusionLogger::getInstance();
		// $logger->log("INFO", "Installation process started");

		$this->initTables();
		$this->createPropertiesFile();
		// $this->injectFooter();

	}

	private function injectFooter() {
		// $logger = FusionLogger::getInstance();
		$footerFile = "modules/Calls/connector/Footer.tpl";
		$destinationFile = "layouts/v7/modules/Vtiger/Footer.tpl";

		$result = copy($footerFile, $destinationFile);
		// $logger->log("INFO", "Copy Footer result is $result");
	}

	private function createPropertiesFile() {
		// $logger = FusionLogger::getInstance();
		$json = json_encode($this->config);

		$prop_file = fopen("properties.json", "w");
		fwrite($prop_file, $json);
		fclose($prop_file);

		// $logger->log("INFO", "Created properties.json file");
	}

	private function initTables() {
		// $logger = FusionLogger::getInstance();

		$conn = new mysqli($this->config['db_server'], $this->config['db_user'], $this->config['db_password']);
		if ($conn->connect_error) {
			// $logger->log("ERROR", "Connection to DB failed: " . $conn->connect_error);
		}

		$conn->query("DROP DATABASE IF EXISTS " . $this->config['db_name'] . ";");
		$sql = "CREATE DATABASE IF NOT EXISTS " . $this->config['db_name'] . " DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";
		if ($conn->query($sql) === true) {
			// $logger->log("INFO", "Database is created");
		}
		else {
			// $logger->log("ERROR", "Error: " . $sql . "<br>" . $conn->error);
		}

		$conn->query("USE " . $this->config['db_name']);
		$sql = file_get_contents($this->config['connector_url'] . "init_db.sql");
		if ($conn->query($sql) === true) {
			// $logger->log("INFO", "Initial schema is created");
		}
		else {
			// $logger->log("ERROR", "Error: " . $sql . "<br>" . $conn->error);
		}

	}

	private function prepareData() {
		include "config.inc.php";

		$config['vtiger_url'] = $site_URL;
		$config['vtiger_db_user'] = $dbconfig['db_username'];
		$config['vtiger_db_pass'] = $dbconfig['db_password'];
		$config['vtiger_db'] = $dbconfig['db_name'];
		$config['db_server'] = $dbconfig['db_server'];
		$config['db_port'] = "3306";
		$config['db_user'] = $dbconfig['db_username'];
		$config['db_password'] = $dbconfig['db_password'];
		$config['db_name'] = 'vtiger_fusion_connector';
		$config['connector_url'] = $site_URL . "modules/Calls/connector/";

		return $config;
	}

}