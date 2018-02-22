<?php

include_once 'include/Webservices/Retrieve.php';

class Calls_GetUserPhone_Action extends Vtiger_Action_Controller {

	public function process(Vtiger_Request $request) {
		$user = Users_Record_Model::getCurrentUserModel();
        $userId = $request->getRaw('userId');
        
        $user = vtws_retrieve("19x" . $userId, $user);
        
		$response = new Vtiger_Response();
		$response->setResult($user["phone_work"]);
		$response->emit();
	}

	public function checkPermission(Vtiger_Request $request) {
		return;
	}
	
}
