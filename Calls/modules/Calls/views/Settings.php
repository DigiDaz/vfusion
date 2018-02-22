<?php

class Calls_Settings_View extends Vtiger_List_View {

    function process(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$viewName = $request->get('viewname');
		if ($viewName) {
			$this->viewName = $viewName;
        }

		$this->initializeListViewContents($request, $viewer);
		$this->assignCustomViews($request, $viewer);
		$viewer->assign('VIEW', $request->get('view'));
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('RECORD_ACTIONS', $this->getRecordActionsFromModule($moduleModel));
		$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());

		$viewer->assign('IS_CREATE_PERMITTED', false);
		$viewer->assign('IS_MODULE_EDITABLE', false);
		$viewer->assign('IS_MODULE_DELETABLE', false);
		$viewer->view('Settings.tpl', $moduleName);
	}

	public function getRecordActionsFromModule($moduleModel) {
		$recordActions = array();
		$recordActions['edit'] = false;
		$recordActions['delete'] = false;
		return $recordActions;
    }
    
}

?>