<?php
/*********************************************************************************
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/
class Default_ProjectsController extends Zend_Controller_Action
{
	private $options;
	/**
	 * Init.
	 *
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_options = $this->getInvokeArg('bootstrap')->getOptions();
	}
	public function preDispatch()
	{
		/*$userModel = new default_Model_Users();
		$checkTmEnable = $userModel->checkTmEnable();

		if(!$checkTmEnable){
			$this->_redirect('error');
		}*/
		
		//check Time management module enable
		if(!sapp_Helper::checkTmEnable())
			$this->_redirect('error');
			
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('addtasksproject', 'html')->initContext();
		$ajaxContext->addActionContext('addtasks', 'html')->initContext();
		$ajaxContext->addActionContext('checkempforprojects', 'json')->initContext();
		//echo Zend_Registry::get( 'tm_role' );
	}

	/**
	 * Grid/List View.
	 */
	public function indexAction()
	{
		$projectModel = new Default_Model_Projects();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall');
		$unitId = intval($this->_getParam('unitId'));
		if(!isset($unitId))
			$unitId = '';

		$data = array();
		$searchQuery = '';
		$searchArray = array();
		$tablecontent='';
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;
			$sort = 'DESC';$by = 'p.modified';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
		}
		else
		{
			$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
			$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'p.modified';
			if($dashboardcall == 'Yes')
			$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
			else
			$perPage = $this->_getParam('per_page',PERPAGE);
			$pageNo = $this->_getParam('page', 1);
			/** search from grid - START **/
			$searchData = $this->_getParam('searchData');
			$searchData = rtrim($searchData,',');
			/** search from grid - END **/
		}
      if(Zend_Registry::get( 'tm_role' ) != 'Employee')
		{
			$dataTmp = $projectModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$unitId);
		}
		else
		{
			$dataTmp = $projectModel->getEmpGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$loginUserId);
		}
			
		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call ;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}

	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'projects';

		$projectModel = new Default_Model_Projects();
		$projectTaskModel = new Timemanagement_Model_Projecttasks();
		$projectResourcesModel = new Timemanagement_Model_Projectresources();
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$auth = Zend_Auth::getInstance();  //echo '<pre>';print_r($auth->getStorage()->read()); exit;
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
				}

				$checkResourceExistsforProject = $projectResourcesModel->checkProjectResource($id,$loginUserId);
				if($loginUserId == 1 || $checkResourceExistsforProject > 0){
					$data = $projectModel->getSingleProjectData($id);
					if(!empty($data) && $data != "norows")
					{
						$data_arr = array();
						$call = $this->_getParam('call');
						if($call == 'ajaxcall')
						$this->_helper->layout->disableLayout();
						$view = Zend_Layout::getMvcInstance()->getView();
						$objname = $this->_getParam('objname');
						$refresh = $this->_getParam('refresh');
						$dashboardcall = $this->_getParam('dashboardcall');

						//$data = array();
						$searchQuery = '';
						$searchArray = array();
						$tablecontent='';

						if($refresh == 'refresh')
						{
							$sort = 'DESC';$by = 'modified';$perPage = DASHBOARD_PERPAGE;$pageNo = 1;$searchData = '';
						}
						else
						{
							$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
							$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modified';
							$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
							$pageNo = $this->_getParam('page', 1);
							$searchData = $this->_getParam('searchData');
						}

						$dataTmp = $projectTaskModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$id);
						$dataTmp['emptyRoles'] = '';
						$dataTmp['objectname'] = 'projecttasks';
						$dataTmp['dataemptyFlag'] = '';
						$dataTmp['menuName'] = 'Tasks';
						$dataTmp['userid'] = '1';
						$dataTmp['dashboardcall'] = 'Yes';
						$dataTmp['projectId'] = $id;
						array_push($data_arr,$dataTmp);

						$dataResourceTmp = $projectResourcesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$id);
						$dataResourceTmp['emptyRoles'] = '';
						$dataResourceTmp['objectname'] = 'projectresources';
						$dataResourceTmp['dataemptyFlag'] = '';
						$dataResourceTmp['menuName'] = 'Resources';
						$dataResourceTmp['userid'] = '1';
						$dataResourceTmp['dashboardcall'] = 'Yes';
						$dataResourceTmp['projectId'] = $id;
						array_push($data_arr,$dataResourceTmp);

						$this->view->data_arr = $data_arr;
						$this->view->controllername = $objName;
						$this->view->data = $data;
						$this->view->id = $id;
						$this->view->ermsg = '';
					}else
					{
						$this->view->ermsg = 'norecord';
					}
				}else
				{
					$this->view->ermsg = 'nodata';
				}
			}else
			{
				$this->view->ermsg = 'nodata';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}
	}

	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$deleteflag=$this->_request->getParam('deleteflag');
		$messages['message'] = ''; $messages['msgtype'] = '';$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$projectModel = new Default_Model_Projects();

			//check project is assigned to employee
			$checkISAssigned = $projectModel->chkProjAssigned($id);
			if($checkISAssigned==0){
				$data = array('is_active'=>0,'modified'=>gmdate("Y-m-d H:i:s"));
				$where = array('id=?'=>$id);
				$Id = $projectModel->SaveorUpdateProjectsData($data, $where);;
				if($Id == 'update')
				{
					$messages['message'] = 'Project deleted successfully.';
					$messages['msgtype'] = 'success';
				}
				else
				{
					$messages['message'] = 'Project cannot be deleted.';
					$messages['msgtype'] = 'error';
				}
			}else{
				$messages['message'] = 'Project is already assigned to employee.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Project cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		// delete success message after delete in view
			if($deleteflag==1)
			{
				if(	$messages['msgtype'] == 'error')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>$messages['message'],"msgtype"=>$messages['msgtype'] ,'deleteflag'=>$deleteflag));
				}
				if(	$messages['msgtype'] == 'success')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>$messages['message'],"msgtype"=>$messages['msgtype'],'deleteflag'=>$deleteflag));
				}
			}
		$this->_helper->json($messages);

	}
	
 public function viewpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
	$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'projects';

		$projectModel = new Default_Model_Projects();
		
		try
		{
			if(is_numeric($id) && $id>0)
			{
				$auth = Zend_Auth::getInstance();  //echo '<pre>';print_r($auth->getStorage()->read()); exit;
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginuserRole = $auth->getStorage()->read()->emprole;
					$loginuserGroup = $auth->getStorage()->read()->group_id;
				}

				
				
					$data = $projectModel->getSingleProjectData($id);
					//echo"<pre>";print_r($data);exit;
					if(!empty($data) && $data != "norows")
					{
						$data_arr = array();
						$call = $this->_getParam('call');
						if($call == 'ajaxcall')
						$this->_helper->layout->disableLayout();
						$view = Zend_Layout::getMvcInstance()->getView();
						$objname = $this->_getParam('objname');
						$refresh = $this->_getParam('refresh');
						$dashboardcall = $this->_getParam('dashboardcall');

						//$data = array();
						$searchQuery = '';
						$searchArray = array();
						$tablecontent='';

						if($refresh == 'refresh')
						{
							$sort = 'DESC';$by = 'modified';$perPage = DASHBOARD_PERPAGE;$pageNo = 1;$searchData = '';
						}
						else
						{
							$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
							$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modified';
							$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
							$pageNo = $this->_getParam('page', 1);
							$searchData = $this->_getParam('searchData');
						}
						 $data[0]['start_date'] =  sapp_Global::change_date($data[0]['start_date'],'view');
                        $data[0]['end_date'] = sapp_Global::change_date($data[0]['end_date'],'view');
					if($data[0]['project_type']=='billable')
					{
						$data[0]['project_type'] = 'Billable';
					}else if($data[0]['project_type']=='non_billable')
					{
						$data[0]['project_type']= 'Non Billable';
					}
					else
					{
						$data[0]['project_type']= 'Revenue generation';
					}
                          
						$this->view->controllername = $objName;
						$this->view->data = $data;
						$this->view->id = $id;
						$this->view->ermsg = '';
					}else
					{
						$this->view->ermsg = 'norecord';
					}
				
			}else
			{
				$this->view->ermsg = 'nodata';
			}
		}
		catch(Exception $e)
		{
			$this->view->ermsg = 'nodata';
		}	
	} 
	
	
	
	
	
	public function editpopupAction()
	{
	
	    Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$unitid = $this->getRequest()->getParam('unitId');
	     $controllername = 'projects';
		$objName = 'projects';$emptyFlag=0;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
        $popup = $this->getRequest()->getParam('popup');
		$popConfigPermission = array();
		$projectsForm = new Default_Form_Projects();
		$projectModel = new Default_Model_Projects();

		$clientModel = new Default_Model_Clients();
		$clientData = $clientModel->getActiveClientsData();
		$msgarray = array();
		$this->view->popConfigPermission = $popConfigPermission;
		
		if(sizeof($clientData) > 0)
		{
			foreach ($clientData as $client){
				$projectsForm->client_id->addMultiOption($client['id'],$client['client_name']);
			}

		}else
		{
			$msgarray['client_id'] = 'Clients are not configured yet.';
			$emptyFlag++;
		}

		$currencyModel = new Default_Model_Currency();
		$currencyData = $currencyModel->getCurrencyList();
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$projectsForm->currency_id->addMultiOption($currency['id'],utf8_encode($currency['currency']));
			}

		}else
		{
			$msgarray['currency_id'] = 'Currency are not configured yet.';
			$emptyFlag++;
		}
	   $projectsForm->setAction(BASE_URL.'projects/editpopup/id/'.$id.'/unitId/'.$unitid);
		$base_projectData = $projectModel->getProjectList();
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$projectsForm->base_project->addMultiOption($base_project['id'],$base_project['project_name']);
			}

		}

		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
		try
		{

			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$auth = Zend_Auth::getInstance(); 
					if($auth->hasIdentity()){
						$loginUserId = $auth->getStorage()->read()->id;
						$loginuserRole = $auth->getStorage()->read()->emprole;
						$loginuserGroup = $auth->getStorage()->read()->group_id;
					}

					$projectResourcesModel = new Timemanagement_Model_Projectresources();
					$checkResourceExistsforProject = $projectResourcesModel->checkProjectResource($id,$loginUserId);
					if($loginUserId == 1 || $checkResourceExistsforProject > 0){
						$data = $projectModel->getSingleProjectData($id);
						if(!empty($data) && $data != "norows")
						{
							
							$projectsForm->populate($data[0]);
							$projectsForm->submit->setLabel('Update');
							if($data[0]['start_date'] !='' && $data[0]['start_date'] !='0000-00-00')
							{
								$estimated_start_date = sapp_Global::change_date($data[0]['start_date'],'view');
								$projectsForm->start_date->setValue($estimated_start_date);
							}

							if($data[0]['end_date'] !='' && $data[0]['end_date'] !='0000-00-00')
							{
								$estimated_end_date = sapp_Global::change_date($data[0]['end_date'],'view');
								$projectsForm->end_date->setValue($estimated_end_date);
							}
							$this->view->form = $projectsForm;
							$this->view->controllername = $objName;
							$this->view->data = $data;
							$this->view->id = $id;
							$this->view->ermsg = '';
							$this->view->inpage = 'Edit';
						}
						else
						{
							$this->view->form = $projectsForm;
							$this->view->controllername = $objName;
							$this->view->data = $data;
							$this->view->ermsg = 'norecord';
							$this->view->inpage = 'Edit';
						}
					}else
					{
						$this->view->form = $projectsForm;
						$this->view->controllername = $objName;
						$this->view->data = array();
						$this->view->ermsg = 'norecord';
						$this->view->inpage = 'Edit';
					}
				}
				else
				{
					$this->view->form = $projectsForm;
					$this->view->controllername = $objName;
					$this->view->data = 'norows';
					$this->view->inpage = 'Edit';
				}
			}else{
				if($this->getRequest()->getParam('cid')){
					$cid = $this->getRequest()->getParam('cid');
					$projectsForm->client_id->setValue($cid);
				}
				$this->view->form = $projectsForm;
				$this->view->ermsg = '';
				$this->view->id = '';
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $ex)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($projectsForm->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$project_name = $this->_request->getParam('project_name');
				$project_status = $this->_request->getParam('project_status');
				$base_project = $this->_request->getParam('base_project');
				$client_id = $this->_request->getParam('client_id');
				$currency_id = $this->_request->getParam('currency_id');
				$project_type = $this->_request->getParam('project_type');
				$start_date = $this->_request->getParam('start_date');
				$start_date = sapp_Global::change_date($start_date,'database');
				$end_date = $this->_request->getParam('end_date');
				$end_date = sapp_Global::change_date($end_date,'database');
				$estimated_hrs = $this->_request->getParam('estimated_hrs');
				$description = $this->_request->getParam('description');

				$date = new Zend_Date();
				$data = array('project_name'=>ucfirst(trim($project_name)),
				              'project_status'=>trim($project_status),
							  'base_project'=>(trim($base_project)!=''?$base_project:NUll),
							  'start_date'=>(trim($start_date)!=''?$start_date:NUll),
							  'end_date'=>(trim($end_date)!=''?$end_date:NUll),
							  'estimated_hrs'=>(trim($estimated_hrs)!=''?$estimated_hrs:NUll),
							  'description'=>trim($description),
							  'client_id'=>trim($client_id),
							  'currency_id'=>trim($currency_id),
							  'project_type'=>trim($project_type),
				              'modified_by'=>$loginUserId,
							  'modified'=>gmdate("Y-m-d H:i:s")
				);

				if($project_status == 'initiated'){
					$data['initiated_date'] = gmdate("Y-m-d H:i:s");
				}
				if($project_status == 'hold'){
					$data['hold_date'] = gmdate("Y-m-d H:i:s");
				}
				if($project_status == 'completed'){
					$data['completed_date'] = gmdate("Y-m-d H:i:s");
				}

				if($id!=''){
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = gmdate("Y-m-d H:i:s");
					$data['is_active'] = 1;
					$where = '';
				}

				$insertedId = $projectModel->SaveorUpdateProjectsData($data, $where);
				if($insertedId == 'update')
				{
					 $this->view->eventact = 'updated';
					 $close = 'close';
					$this->view->popup=$close;
					$this->view->controllername = $controllername;
					
				}
				else
				{
					if(Zend_Registry::get( 'tm_role' ) == 'Manager'){
						$projectResourcesModel = new Default_Model_Projectresources();
						$projectResourceData = array( 'project_id'=>trim($insertedId),
										              'emp_id'=>$loginUserId, 
													  'created_by' => $loginUserId,
													  'created' =>gmdate("Y-m-d H:i:s"),
													  'is_active' => 1,
										   			  'modified_by'=>$loginUserId,
													  'modified'=>gmdate("Y-m-d H:i:s")	
						);
						$result = $projectResourcesModel->SaveorUpdateProjectResourceData($projectResourceData,'');
					}
					
					Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
					
					$id = $insertedId;
					$this->view->eventact = 'added';
					$close = 'close';
					$this->view->popup=$close;
					$this->view->controllername = $controllername;
					
				}

				
			}else
			{
				$messages = $projectsForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}

				if(sizeof($clientData) < 1)
				$msgarray['client_id'] = 'Clients not configured yet.';
				if(sizeof($currencyData) < 1)
				$msgarray['currency_id'] = 'Currency not configured yet.';
				$this->view->msgarray = $msgarray;
			}
		}

	
	}
	/*
	 * Edit View
	 */
	public function editAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}

		$objName = 'projects';$emptyFlag=0;
		$id = $this->getRequest()->getParam('id');
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();

		$popConfigPermission = array();
		$projectsForm = new Timemanagement_Form_Projects();
		$projectModel = new Timemanagement_Model_Projects();

		$clientModel = new Timemanagement_Model_Clients();
		$clientData = $clientModel->getActiveClientsData();
		$msgarray = array();
		array_push($popConfigPermission,'client');
		if(sapp_Global::_checkprivileges(CURRENCY,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
		 		array_push($popConfigPermission,'currency');
		 	}
		$this->view->popConfigPermission = $popConfigPermission;
		if(sizeof($clientData) > 0)
		{
			foreach ($clientData as $client){
				$projectsForm->client_id->addMultiOption($client['id'],$client['client_name']);
			}

		}else
		{
			$msgarray['client_id'] = 'Clients are not configured yet.';
			$emptyFlag++;
		}

		$currencyModel = new Default_Model_Currency();
		$currencyData = $currencyModel->getCurrencyList();
		if(sizeof($currencyData) > 0)
		{
			foreach ($currencyData as $currency){
				$projectsForm->currency_id->addMultiOption($currency['id'],utf8_encode($currency['currency']));
			}

		}else
		{
			$msgarray['currency_id'] = 'Currency are not configured yet.';
			$emptyFlag++;
		}

		$base_projectData = $projectModel->getProjectList();
		if(sizeof($base_projectData) > 0)
		{
			foreach ($base_projectData as $base_project){
				$projectsForm->base_project->addMultiOption($base_project['id'],$base_project['project_name']);
			}

		}

		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
		try
		{

			if($id)
			{	//Edit Record...
				if(is_numeric($id) && $id>0)
				{
					$auth = Zend_Auth::getInstance();
					if($auth->hasIdentity()){
						$loginUserId = $auth->getStorage()->read()->id;
						$loginuserRole = $auth->getStorage()->read()->emprole;
						$loginuserGroup = $auth->getStorage()->read()->group_id;
					}

					$projectResourcesModel = new Timemanagement_Model_Projectresources();
					$checkResourceExistsforProject = $projectResourcesModel->checkProjectResource($id,$loginUserId);
					if($loginUserId == 1 || $checkResourceExistsforProject > 0){
						$data = $projectModel->getSingleProjectData($id);
						if(!empty($data) && $data != "norows")
						{
							
							$projectsForm->populate($data[0]);
							$projectsForm->submit->setLabel('Update');
							if($data[0]['start_date'] !='' && $data[0]['start_date'] !='0000-00-00')
							{
								$estimated_start_date = sapp_Global::change_date($data[0]['start_date'],'view');
								$projectsForm->start_date->setValue($estimated_start_date);
							}

							if($data[0]['end_date'] !='' && $data[0]['end_date'] !='0000-00-00')
							{
								$estimated_end_date = sapp_Global::change_date($data[0]['end_date'],'view');
								$projectsForm->end_date->setValue($estimated_end_date);
							}
							$this->view->form = $projectsForm;
							$this->view->controllername = $objName;
							$this->view->data = $data;
							$this->view->id = $id;
							$this->view->ermsg = '';
							$this->view->inpage = 'Edit';
						}
						else
						{
							$this->view->form = $projectsForm;
							$this->view->controllername = $objName;
							$this->view->data = $data;
							$this->view->ermsg = 'norecord';
							$this->view->inpage = 'Edit';
						}
					}else
					{
						$this->view->form = $projectsForm;
						$this->view->controllername = $objName;
						$this->view->data = array();
						$this->view->ermsg = 'norecord';
						$this->view->inpage = 'Edit';
					}
				}
				else
				{
					$this->view->form = $projectsForm;
					$this->view->controllername = $objName;
					$this->view->data = 'norows';
					$this->view->inpage = 'Edit';
				}
			}else{
				if($this->getRequest()->getParam('cid')){
					$cid = $this->getRequest()->getParam('cid');
					$projectsForm->client_id->setValue($cid);
				}
				$this->view->form = $projectsForm;
				$this->view->ermsg = '';
				$this->view->id = '';
				$this->view->inpage = 'Add';
			}
		}
		catch(Exception $ex)
		{
			$this->view->ermsg = 'nodata';
		}
		if($this->getRequest()->getPost()){
			if($projectsForm->isValid($this->_request->getPost())){
				$id = $this->_request->getParam('id');
				$project_name = $this->_request->getParam('project_name');
				$project_status = $this->_request->getParam('project_status');
				$base_project = $this->_request->getParam('base_project');
				$client_id = $this->_request->getParam('client_id');
				$currency_id = $this->_request->getParam('currency_id');
				$project_type = $this->_request->getParam('project_type');
				$start_date = $this->_request->getParam('start_date');
				$start_date = sapp_Global::change_date($start_date,'database');
				$end_date = $this->_request->getParam('end_date');
				$end_date = sapp_Global::change_date($end_date,'database');
				$estimated_hrs = $this->_request->getParam('estimated_hrs');
				$description = $this->_request->getParam('description');

				$date = new Zend_Date();
				$data = array('project_name'=>ucfirst(trim($project_name)),
				              'project_status'=>trim($project_status),
							  'base_project'=>(trim($base_project)!=''?$base_project:NUll),
							  'start_date'=>(trim($start_date)!=''?$start_date:NUll),
							  'end_date'=>(trim($end_date)!=''?$end_date:NUll),
							  'estimated_hrs'=>(trim($estimated_hrs)!=''?$estimated_hrs:NUll),
							  'description'=>trim($description),
							  'client_id'=>trim($client_id),
							  'currency_id'=>trim($currency_id),
							  'project_type'=>trim($project_type),
				              'modified_by'=>$loginUserId,
							  'modified'=>gmdate("Y-m-d H:i:s")
				);

				if($project_status == 'initiated'){
					$data['initiated_date'] = gmdate("Y-m-d H:i:s");
				}
				if($project_status == 'hold'){
					$data['hold_date'] = gmdate("Y-m-d H:i:s");
				}
				if($project_status == 'completed'){
					$data['completed_date'] = gmdate("Y-m-d H:i:s");
				}

				if($id!=''){
					$where = array('id=?'=>$id);
				}
				else
				{
					$data['created_by'] = $loginUserId;
					$data['created'] = gmdate("Y-m-d H:i:s");
					$data['is_active'] = 1;
					$where = '';
				}

				$insertedId = $projectModel->SaveorUpdateProjectsData($data, $where);
				if($insertedId == 'update')
				{
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Project updated successfully."));
				}
				else
				{
					 if(Zend_Registry::get( 'tm_role' ) == 'Manager'){
						$projectResourcesModel = new Timemanagement_Model_Projectresources();
						$projectResourceData = array( 'project_id'=>trim($insertedId),
										              'emp_id'=>$loginUserId, 
													  'created_by' => $loginUserId,
													  'created' =>gmdate("Y-m-d H:i:s"),
													  'is_active' => 1,
										   			  'modified_by'=>$loginUserId,
													  'modified'=>gmdate("Y-m-d H:i:s")	
						);
						$result = $projectResourcesModel->SaveorUpdateProjectResourceData($projectResourceData,'');
					}
					$id = $insertedId; 
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Project added successfully."));
				}

				$this->_redirect('projects');
			}else
			{
				$messages = $projectsForm->getMessages();
				foreach ($messages as $key => $val)
				{
					foreach($val as $key2 => $val2)
					{
						$msgarray[$key] = $val2;
						break;
					}
				}

				if(sizeof($clientData) < 1)
				$msgarray['client_id'] = 'Clients not configured yet.';
				if(sizeof($currencyData) < 1)
				$msgarray['currency_id'] = 'Currency not configured yet.';
				$this->view->msgarray = $msgarray;
			}
		}
	}

}