<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_EmpjobhistoryController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{

	}

	public function init()
	{
		$employeeModel = new Default_Model_Employee();
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}
	public function indexAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_jobhistory',$empOrganizationTabs)){
		 	$userID="";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$conText ='';
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	 
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0)
				{
					$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$empjobhistoryModel = new Default_Model_Empjobhistory();
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;

								$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);

								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empjobhistoryModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
							$this->view->id = $id ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
		 		    }
				}	
				else
				{
				   $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function editAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_jobhistory',$empOrganizationTabs)){
		 	$userID="";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$conText ='';
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	 
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0 && $Uid!=$loginUserId)
				{
					$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$empjobhistoryModel = new Default_Model_Empjobhistory();
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;

								$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);
									
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empjobhistoryModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call;
							$this->view->employeedata = $employeeData[0];
							$this->view->id = $id ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
					}
				}	
				else
				{
				   $this->view->rowexist = "norows";
				}
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('emp_jobhistory',$empOrganizationTabs)){
		 	$userID="";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$conText ='';
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	 
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($Uid && is_numeric($Uid) && $Uid>0 && $Uid!=$loginUserId)
				{
					$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($Uid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					if($empdata == 'norows')
					{
						$this->view->rowexist = "norows";
						$this->view->empdata = "";
					}
					else
					{
						$this->view->rowexist = "rows";
						if(!empty($empdata))
						{
							$empjobhistoryModel = new Default_Model_Empjobhistory();
							$view = Zend_Layout::getMvcInstance()->getView();
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();		$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;

								$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'e.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $empjobhistoryModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
							$this->view->id = $id ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
						}
						$this->view->empdata = $empdata;
					}
				}	
				else
				{
				   $this->view->rowexist = "norows";
				}	
		 	}
		 	catch(Exception $e)
		 	{
		 		$this->view->rowexist = "norows";
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function addpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
                $popConfigPermission = array();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
                        $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
        if(sapp_Global::_checkprivileges(JOBTITLES,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'jobtitles');
		}
		if(sapp_Global::_checkprivileges(POSITIONS,$loginuserGroup,$loginuserRole,'add') == 'Yes'){
			array_push($popConfigPermission,'position');
		}
        $this->view->popConfigPermission = $popConfigPermission;
		$id = $this->getRequest()->getParam('unitId');
		if($id == '')
		$id = $loginUserId;
		// For open the form in popup...
		$empjobhistoryform = new Default_Form_empjobhistory();
		$emptyFlag=0;
		$msgarray = array();
		$employeeModel = new Default_Model_Employee();
		$positionModel = new Default_Model_Positions();
		$departmentModel = new Default_Model_Departments();
		$jobtitleModel = new Default_Model_Jobtitles();
		$clientsModel = new Timemanagement_Model_Clients();

		/* To check business unit exists for that particular employee
		 If exists then bring departments for that particular business unit else bring all departments
		 */
		$employeeArr = $employeeModel->getActiveEmployeeData($id);
		if(!empty($employeeArr))
		{
			if(isset($employeeArr[0]['businessunit_id']) && $employeeArr[0]['businessunit_id'] !='')
			{
				$departmentArr = $departmentModel->getDepartmentList($employeeArr[0]['businessunit_id']);
				if(!empty($departmentArr))
				{
					$empjobhistoryform->department->addMultiOption('','Select Department');
					foreach ($departmentArr as $departmentres){
						$empjobhistoryform->department->addMultiOption($departmentres['id'],$departmentres['deptname']);

					}
				}
				else
				{
					$msgarray['department'] = 'Departments are not added yet.';
					$emptyFlag++;
				}
			}
			else
			{
				$departmentArr = $departmentModel->getTotalDepartmentList();
				if(!empty($departmentArr))
				{
					$empjobhistoryform->department->addMultiOption('','Select Department');
					foreach ($departmentArr as $departmentres){
						$empjobhistoryform->department->addMultiOption($departmentres['id'],$departmentres['deptname']);

					}
				}
				else
				{
					$msgarray['department'] = 'Departments are not added yet.';
					$emptyFlag++;
				}
			}
		}
		$positionArr = $positionModel->getTotalPositionList();
		$empjobhistoryform->positionheld->addMultiOption('','Select Position');
		if(!empty($positionArr))
		{
			foreach ($positionArr as $positionres){
				$empjobhistoryform->positionheld->addMultiOption($positionres['id'],$positionres['positionname']);	
			}
		}else
		{
			$msgarray['positionheld'] = 'Positions are not configured yet.';
			$emptyFlag++;
		}
		$jobtitleArr = $jobtitleModel->getJobTitleList();
		$empjobhistoryform->jobtitleid->addMultiOption('','Select Job Title');
		if(!empty($jobtitleArr))
		{
			foreach ($jobtitleArr as $jobtitleres){
				$empjobhistoryform->jobtitleid->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);
					
			}
		}
		else
		{
			$msgarray['jobtitleid'] = 'Job titles are not configured yet.';
			$emptyFlag++;
		}
		$clientsArr = $clientsModel->getActiveClientsData();
		$empjobhistoryform->client->addMultiOption('','Select a Client');
		if(!empty($clientsArr))
		{
			foreach ($clientsArr as $clientsres){
				$empjobhistoryform->client->addMultiOption($clientsres['id'],$clientsres['client_name']);
			}
		}
		else
		{
			$msgarray['client'] = 'Clients are not configured yet.';
			$emptyFlag++;
		}
		$empjobhistoryform->setAttrib('action',BASE_URL.'empjobhistory/addpopup/unitId/'.$id);
		$this->view->form = $empjobhistoryform;
		$this->view->controllername = 'empjobhistory';
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($empjobhistoryform,$id);
			$this->view->msgarray = $result;
		}

	}

	public function viewpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		if($id == '')
		$id = $loginUserId;
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'empjobhistory';
		$empjobhistoryform = new Default_Form_empjobhistory();
		$empjobhistoryModel = new Default_Model_Empjobhistory();
		$employeeModel = new Default_Model_Employee();
		$positionModel = new Default_Model_Positions();
		$departmentModel = new Default_Model_Departments();
		$jobtitleModel = new Default_Model_Jobtitles();
		$empjobhistoryform->removeElement("submit");
		$elements = $empjobhistoryform->getElements();
		$clientsModel = new Timemanagement_Model_Clients();
		
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		if($id)
		{
			$data = $empjobhistoryModel->getsingleEmpJobHistoryData($id);
			if(!empty($data))
			{
				$positionheldArr = 'norows';
				if(is_numeric($data[0]['positionheld']))
				{
					$positionheldArr = $positionModel->getsinglePositionData($data[0]['positionheld']);
				}
				if($positionheldArr != 'norows'){
					$empjobhistoryform->positionheld->addMultiOption($positionheldArr[0]['id'],$positionheldArr[0]['positionname']);
					$data[0]['positionheld']=$positionheldArr[0]['positionname'];
				}
				else{
					$data[0]['positionheld']="";
				}
				$departmentArr = array();
				if(is_numeric($data[0]['department']))
				{
					$departmentArr = $departmentModel->getSingleDepartmentData($data[0]['department']);
				}
				if(!empty($departmentArr))
				{
					$empjobhistoryform->department->addMultiOption($departmentArr['id'],$departmentArr['deptname']);
					$data[0]['department']=$departmentArr['deptname'];
				}
				else
				{
					$data[0]['department']="";
				}
				$jobtitleArr = 'norows';
				if(is_numeric($data[0]['jobtitleid']))
				{				
					$jobtitleArr = $jobtitleModel->getsingleJobTitleData($data[0]['jobtitleid']);
				
					if($jobtitleArr !='norows')
					{
						$empjobhistoryform->jobtitleid->addMultiOption($jobtitleArr[0]['id'],$jobtitleArr[0]['jobtitlename']);
						$data[0]['jobtitleid']=$jobtitleArr[0]['jobtitlename'];
					}
					else
					{
						$data[0]['jobtitleid']="";
					}
				}
				$clientsArr = array();
				if(is_numeric($data[0]['client_id']))
				{				
					$clientsArr = $clientsModel->getClientDetailsById($data[0]['client_id']);
				}
				if(!empty($clientsArr))
				{
					$empjobhistoryform->client->addMultiOption($clientsArr[0]['id'],$clientsArr[0]['client_name']);
					$data[0]['client_id']=$clientsArr[0]['client_name'];
				}
                else
				{
					$data[0]['client_id']=  "";
				}					
				$empjobhistoryform->populate($data[0]);
				if(isset($data[0]['start_date']) && $data[0]['start_date'] !='')
				{
					$start_date = sapp_Global::change_date($data[0]['start_date'], 'view');
					$empjobhistoryform->start_date->setValue($start_date);
				}
				if(isset($data[0]['end_date']) && $data[0]['end_date'] !='')
				{
					$end_date = sapp_Global::change_date($data[0]['end_date'], 'view');
					$empjobhistoryform->end_date->setValue($end_date);
				}
               
			}
			$this->view->controllername = $objName;
			$this->view->id = $id;
			$this->view->data = $data[0];
			$this->view->form = $empjobhistoryform;
		}
	}

	public function editpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$userid = $this->getRequest()->getParam('unitId');
		if($id == '')
		$id = $loginUserId;
		// For open the form in popup...
		$empjobhistoryform = new Default_Form_empjobhistory();
		$empjobhistoryModel = new Default_Model_Empjobhistory();
		$employeeModel = new Default_Model_Employee();
		$positionModel = new Default_Model_Positions();
		$departmentModel = new Default_Model_Departments();
		$jobtitleModel = new Default_Model_Jobtitles();
		$clientsModel = new Timemanagement_Model_Clients();
		
		if($id)
		{
			$employeeArr = $employeeModel->getActiveEmployeeData($userid);
			if(!empty($employeeArr))
			{
				if(isset($employeeArr[0]['businessunit_id']) && $employeeArr[0]['businessunit_id'] !='')
				{
					$departmentArr = $departmentModel->getDepartmentList($employeeArr[0]['businessunit_id']);
					if(!empty($departmentArr))
					{
						$empjobhistoryform->department->addMultiOption('','Select Department');
						foreach ($departmentArr as $departmentres){
							$empjobhistoryform->department->addMultiOption($departmentres['id'],$departmentres['deptname']);
						}
					}
				}else
				{
					$departmentArr = $departmentModel->getTotalDepartmentList();
					if(!empty($departmentArr))
					{
						$empjobhistoryform->department->addMultiOption('','Select Department');
						foreach ($departmentArr as $departmentres){
							$empjobhistoryform->department->addMultiOption($departmentres['id'],$departmentres['deptname']);

						}
					}
				}
			}

			$positionArr = $positionModel->getTotalPositionList();
			if(!empty($positionArr))
			{
				$empjobhistoryform->positionheld->addMultiOption('','Select Position');
				foreach ($positionArr as $positionres){
					$empjobhistoryform->positionheld->addMultiOption($positionres['id'],$positionres['positionname']);

				}
			}
			$jobtitleArr = $jobtitleModel->getJobTitleList();
			if(!empty($jobtitleArr))
			{
				$empjobhistoryform->jobtitleid->addMultiOption('','Select Job Title');
				foreach ($jobtitleArr as $jobtitleres){
					$empjobhistoryform->jobtitleid->addMultiOption($jobtitleres['id'],$jobtitleres['jobtitlename']);

				}
			}
			$clientsArr = $clientsModel->getActiveClientsData();
			if(!empty($clientsArr))
			{
				$empjobhistoryform->client->addMultiOption('','Select a Client');
				foreach ($clientsArr as $clientsres){
					$empjobhistoryform->client->addMultiOption($clientsres['id'],$clientsres['client_name']);
				}
			}			
			$data = $empjobhistoryModel->getsingleEmpJobHistoryData($id);
			if(!empty($data))
			{
				$empjobhistoryform->populate($data[0]);
				$empjobhistoryform->setDefault('department',$data[0]['department']);
				$empjobhistoryform->setDefault('positionheld',$data[0]['positionheld']);
				$empjobhistoryform->setDefault('jobtitleid',$data[0]['jobtitleid']);
				$empjobhistoryform->setDefault('client',$data[0]['client_id']);
				if(isset($data[0]['start_date']) && $data[0]['start_date'] !='')
				{
					$start_date = sapp_Global::change_date($data[0]['start_date'], 'view');
					$empjobhistoryform->start_date->setValue($start_date);
				}
				if(isset($data[0]['end_date']) && $data[0]['end_date'] !='')
				{
					$end_date = sapp_Global::change_date($data[0]['end_date'], 'view');
					$empjobhistoryform->end_date->setValue($end_date);
				}

			}
		}
		$empjobhistoryform->setAttrib('action',BASE_URL.'empjobhistory/editpopup/unitId/'.$userid);
		$this->view->form = $empjobhistoryform;
		$this->view->controllername = 'empjobhistory';

		if($this->getRequest()->getPost())
		{	
			$result = $this->save($empjobhistoryform,$userid);
			$this->view->msgarray = $result;
		}

	}


	public function save($empjobhistoryform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		
		if($empjobhistoryform->isValid($this->_request->getPost()))
		{
			$client = $this->_request->getParam('client');
			$start_date = $this->_request->getParam('start_date',null);
			$start_date = sapp_Global::change_date($start_date, 'database');
			$empjobhistoryModel = new Default_Model_Empjobhistory();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$positionheld = $this->_request->getParam('positionheld');
			$department = $this->_request->getParam('department');
			$jobtitleid = $this->_request->getParam('jobtitleid');
			
			$vendor = $this->_request->getParam('vendor');
			$receiving_amt = $this->_request->getParam('received_amount');
			$paying_amt = $this->_request->getParam('paid_amount');

			$end_date = $this->_request->getParam('end_date',null);
			$end_date = sapp_Global::change_date($end_date, 'database');

			$active_company = $this->_request->getParam('active_company',null);

			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';

			$data = array('user_id'=>$user_id,
								 'positionheld'=>$positionheld,
								 'department'=>$department,
								 'jobtitleid'=>$jobtitleid,
								 'start_date'=>($start_date!=''?$start_date:NUll),
								 'end_date'=>($end_date!=''?$end_date:NUll), 								 
								 'active_company'=>($active_company!=''?$active_company:NUll),
								 'client_id'=>($client!=''?$client:NUll),
								 'vendor'=>($vendor!=''?$vendor:NUll),
								 'received_amount'=>(is_numeric($receiving_amt)?$receiving_amt:NUll),
								 'paid_amount'=>(is_numeric($paying_amt)?$paying_amt:NUll),
								 'modifiedby'=>$loginUserId,
								 'modifieddate'=>gmdate("Y-m-d H:i:s")			
			);
			if($id!=''){
				$where = array('id=?'=>$id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$data['isactive'] = 1;
				$where = '';
				$actionflag = 1;
			}
			$Id = $empjobhistoryModel->SaveorUpdateEmpJobHistory($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee job history updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee job history added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->view->controllername = 'empjobhistory';
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		}else
		{
			$messages = $empjobhistoryform->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				{
					$msgarray[$key] = $val2;
					break;
				}
			}
			return $msgarray;
		}

	}

	public function deleteAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->_request->getParam('objid');
		$messages['message'] = '';
		$messages['msgtype'] = '';
		$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$empjobhistoryModel = new Default_Model_Empjobhistory();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $empjobhistoryModel->SaveorUpdateEmpJobHistory($data, $where);
			if($Id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employee job history deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Employee job history cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Employee job history cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);

	}



}