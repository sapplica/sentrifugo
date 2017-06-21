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

class Default_MedicalclaimsController extends Zend_Controller_Action
{
	private $options;

	public function preDispatch()
	{

	}

	public function init()
	{
		$this->_options= $this->getInvokeArg('bootstrap')->getOptions();
	}

	public function indexAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('medical_claims',$empOrganizationTabs)){
		 	$userID="";$conText = "";$searchArr=array('injury','disability','maternity','paternity');$objName="medicalclaims";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');

		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid = $userID;
		 	$Uid = ($userid)?$userid:$userID;

		 	$empMedicalclaimsform = new Default_Form_Medicalclaims();
		 	$empMedicalclaimsModel = new Default_Model_Medicalclaims();
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
		 				$view = Zend_Layout::getMvcInstance()->getView();
		 				$objname = $this->_getParam('objname');
		 				$refresh = $this->_getParam('refresh');
		 				$dashboardcall = $this->_getParam('dashboardcall',null);
		 				$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';
		 				if($refresh == 'refresh')
		 				{
		 					if($dashboardcall == 'Yes')
		 					$perPage = DASHBOARD_PERPAGE;
		 					else
		 					$perPage = PERPAGE;

		 					$sort = 'DESC';$by = 'm.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
		 				}
		 				else
		 				{
		 					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
		 					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'m.modifieddate';
		 					if($dashboardcall == 'Yes')
		 					$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
		 					else
		 					$perPage = $this->_getParam('per_page',PERPAGE);

		 					$pageNo = $this->_getParam('page', 1);
		 					$searchData = $this->_getParam('searchData');
		 					$searchData = rtrim($searchData,',');
		 				}
		 				$dataTmp = $empMedicalclaimsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

		 				array_push($data,$dataTmp);
		 				$this->view->id=$userid;
		 				$this->view->controllername = $objName;
		 				$this->view->dataArray = $data;
		 				$this->view->employeedata = $employeeData[0];
		 				$this->view->call = $call ;
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

		 if(in_array('medical_claims',$empOrganizationTabs)){
		 	$userID="";$conText = "";$searchArr=array('injury','disability','maternity','paternity');$objName='medicalclaims';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');

		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid = $userID;
		 	$Uid = ($userid)?$userid:$userID;

		 	$empMedicalclaimsform = new Default_Form_Medicalclaims();
		 	$empMedicalclaimsModel = new Default_Model_Medicalclaims();
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
		 				$view = Zend_Layout::getMvcInstance()->getView();
		 				$objname = $this->_getParam('objname');
		 				$refresh = $this->_getParam('refresh');
		 				$dashboardcall = $this->_getParam('dashboardcall',null);
		 				$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';
		 				if($refresh == 'refresh')
		 				{
		 					if($dashboardcall == 'Yes')
		 					$perPage = DASHBOARD_PERPAGE;
		 					else
		 					$perPage = PERPAGE;

		 					$sort = 'DESC';$by = 'm.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
		 				}
		 				else
		 				{
		 					$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
		 					$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'m.modifieddate';
		 					if($dashboardcall == 'Yes')
		 					$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
		 					else
		 					$perPage = $this->_getParam('per_page',PERPAGE);

		 					$pageNo = $this->_getParam('page', 1);
		 					$searchData = $this->_getParam('searchData');
		 					$searchData = rtrim($searchData,',');
		 				}
		 				$dataTmp = $empMedicalclaimsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
                       
		 				array_push($data,$dataTmp);
		 				$this->view->id=$userid;
		 				$this->view->controllername = $objName;
		 				$this->view->dataArray = $data;
		 				$this->view->employeedata = $employeeData[0];
		 				$this->view->call = $call ;
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
		$msgarray = array();
		$weekendDatailsArr = array();
		$is_orghead = '';
		$weekendDatailsArr = array();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
			$group_id = $auth->getStorage()->read()->group_id;
		}
		$leavemanagementmodel = new Default_Model_Leavemanagement();
		$employeesmodel = new Default_Model_Employees();
		$userId = $this->getRequest()->getParam('unitId');
		
		if($userId)
		{
			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($userId);
			if(!empty($loggedInEmployeeDetails))
			{
			$is_orghead = $loggedInEmployeeDetails[0]['is_orghead'];
			$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
				$weekendDatailsArr = $leavemanagementmodel->getWeekendDetails($employeeDepartmentId);
			}	
				

			// For open the form in popup...
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
			$empMedicalclaimsform = new Default_Form_Medicalclaims();
				
			$empMedicalclaimsform->setAttrib('action',BASE_URL.'medicalclaims/addpopup/unitId/'.$userId);
			$empMedicalclaimsform->setDefault("user_id",$userId);
			$this->view->form = $empMedicalclaimsform;
			$this->view->msgarray = $msgarray;
			$this->view->weekendDatailsArr = $weekendDatailsArr;
			$this->view->is_orghead = $is_orghead;
			$this->view->group_id = $group_id;
		}

		if($this->getRequest()->getPost())
		{
			$errorResult = $this->medicalclaimsvalidations();
			if(empty($errorResult['msgarray']))
			{
				$result = $this->save($empMedicalclaimsform,$userId);
				$this->view->msgarray = $result;
				$this->view->fieldValues = $errorResult['fieldValues'];
			}
			else
			{
				$this->view->msgarray = $errorResult['msgarray'];
				$this->view->fieldValues = $errorResult['fieldValues'];
			}
		}
		else
		{

		}
		
			

	}
	public function viewpopupAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');
		$auth = Zend_Auth::getInstance();
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");

		$empMedicalclaimsform = new Default_Form_Medicalclaims();
		$empMedicalclaimsModel = new Default_Model_Medicalclaims();

		$empMedicalclaimsform->removeElement("submit");
		$elements = $empMedicalclaimsform->getElements();
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
			$data = $empMedicalclaimsModel->getmedicalclaimsdetails($id);
			if(!empty($data))
			{
				$empMedicalclaimsform->setDefault("id",$id);
				$empMedicalclaimsform->setDefault("user_id",$data[0]["user_id"]);
				$empMedicalclaimsform->setDefault("injury_severity",$data[0]["injury_severity"]);
                if(!empty($data[0]["injury_type"])){
                	if($data[0]["injury_type"]=='1'){
                	  $data[0]["injury_type"]="Paternity";
                	}elseif($data[0]["injury_type"]=='2'){
                	 $data[0]["injury_type"]="Maternity";
                	}elseif($data[0]["injury_type"]=='3'){
                	 $data[0]["injury_type"]="Disability";
                	}else{
                	 $data[0]["injury_type"]="Injury";
                	}
                }
			 if(!empty($data[0]["injury_severity"])){
                	if($data[0]["injury_severity"]=='1'){
                	  $data[0]["injury_severity"]="Major";
                	}else{
                	 $data[0]["injury_severity"]="Minor";
                	}
			 }
				$empMedicalclaimsform->setDefault("type",$data[0]["injury_type"]);
				//echo"<pre>";print_r($data);exit;
				$empMedicalclaimsform->setDefault("description",$data[0]["injury_description"]);
				$empMedicalclaimsform->setDefault("injury_name",$data[0]["injury_name"]);
				$empMedicalclaimsform->setDefault("disability_type",$data[0]["disability_type"]);
				$empMedicalclaimsform->setDefault("other_disability_type",$data[0]["other_disability_type"]);
				$empMedicalclaimsform->setDefault("insurer_name",$data[0]["medical_insurer_name"]);
				$empMedicalclaimsform->setDefault("gp_name",$data[0]["concerned_physician_name"]);
				$empMedicalclaimsform->setDefault("hospital_name",$data[0]["hospital_name"]);
				$empMedicalclaimsform->setDefault("hospital_addr",$data[0]["hospital_address"]);
				$empMedicalclaimsform->setDefault("treatment_details",$data[0]["treatment_details"]);
				$empMedicalclaimsform->setDefault("room_num",$data[0]["room_number"]);
				$empMedicalclaimsform->setDefault("total_cost",$data[0]["total_cost"]);
				$empMedicalclaimsform->setDefault("amount_claimed",$data[0]["amount_claimed_for"]);
				$empMedicalclaimsform->setDefault("amount_approved",$data[0]["amount_approved"]);
				$empMedicalclaimsform->setDefault("leavebyemp_days",$data[0]["leavebyemployeer_days"]);
				$empMedicalclaimsform->setDefault("empleave_days",$data[0]["leaveappliedbyemployee_days"]);


				if($data[0]["injured_date"] != "" && $data[0]["injured_date"] != 0000-00-00)
				{
					$injureddate =sapp_Global::change_date($data[0]["injured_date"], 'view');
					$empMedicalclaimsform->setDefault('injured_date', $injureddate);
				}

				if($data[0]["expected_date_join"] != "" && $data[0]["expected_date_join"] != 0000-00-00)
				{
					$exp_dateofjoin =sapp_Global::change_date($data[0]["expected_date_join"], 'view');
					$empMedicalclaimsform->setDefault('expected_date_join', $exp_dateofjoin);
				}

				if($data[0]["leavebyemployeer_to_date"] != "" && $data[0]["leavebyemployeer_to_date"] != 0000-00-00)
				{
					$leavebyemployeer_to_date =sapp_Global::change_date($data[0]["leavebyemployeer_to_date"], 'view');
					$empMedicalclaimsform->setDefault("leavebyemp_to_date",$leavebyemployeer_to_date);
				}
				if($data[0]["leavebyemployeer_from_date"] != "" && $data[0]["leavebyemployeer_from_date"] != 0000-00-00)
				{
					$leavebyemployeer_from_date = sapp_Global::change_date($data[0]["leavebyemployeer_from_date"], 'view');
					$empMedicalclaimsform->setDefault("leavebyemp_from_date",$leavebyemployeer_from_date);
				}
				if($data[0]["leaveappliedbyemployee_to_date"] != "" && $data[0]["leaveappliedbyemployee_to_date"] != 0000-00-00)
				{
					$leaveappliedbyemployeetodate = sapp_Global::change_date($data[0]["leaveappliedbyemployee_to_date"], 'view');
					$empMedicalclaimsform->setDefault("empleave_to_date",$leaveappliedbyemployeetodate);
				}
				if($data[0]["leaveappliedbyemployee_from_date"] != "" && $data[0]["leaveappliedbyemployee_from_date"] != 0000-00-00)
				{
					$leaveappliedbyemployeefromdate = sapp_Global::change_date($data[0]["leaveappliedbyemployee_from_date"], 'view');
					$empMedicalclaimsform->setDefault("empleave_from_date",$leaveappliedbyemployeefromdate);
				}

			}
			
			$this->view->id=$id;
			$this->view->data=$data[0];
			
		}
		$this->view->form = $empMedicalclaimsform;
		$this->view->controllername = 'medicalclaims';
	}

	public function editpopupAction()
	{
		//For opening the form in pop up.....
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$leavemanagementmodel = new Default_Model_Leavemanagement();
		$employeesmodel = new Default_Model_Employees();
		$empMedicalclaimsform = new Default_Form_Medicalclaims();
		$empMedicalclaimsModel = new Default_Model_Medicalclaims();

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
			$loginUserGroup = $auth->getStorage()->read()->id;
		}
		$id = $this->getRequest()->getParam('id');	//Id (PK) from form URL
		$user_id = $this->getRequest()->getParam('unitId');	//This is User_id taking from URL set to form...
		
		if($user_id)
		{
			$loggedInEmployeeDetails = $employeesmodel->getLoggedInEmployeeDetails($user_id);
			$employeeDepartmentId = $loggedInEmployeeDetails[0]['department_id'];
			$weekendDatailsArr = $leavemanagementmodel->getWeekendDetails($employeeDepartmentId);
			$this->view->weekendDatailsArr = $weekendDatailsArr;
		}	

		$empMedicalclaimsform ->removeElement('injuryindicator');
		$empMedicalclaimsform ->removeElement('type');

		if($id)
		{
			$data = $empMedicalclaimsModel->getmedicalclaimsdetails($id);
			if(!empty($data))
			{
				$empMedicalclaimsform->setDefault("id",$data[0]["id"]);
				$empMedicalclaimsform->setDefault("user_id",$data[0]["user_id"]);
				$empMedicalclaimsform->setDefault("injuryindicator",$data[0]["injury_indicator"]);
				$empMedicalclaimsform->setDefault("type",$data[0]["injury_type"]);
				$empMedicalclaimsform->setDefault("description",$data[0]["injury_description"]);
				$empMedicalclaimsform->setDefault("injury_name",$data[0]["injury_name"]);
				$empMedicalclaimsform->setDefault("injury_severity",$data[0]["injury_severity"]);
				$empMedicalclaimsform->setDefault("disability_type",$data[0]["disability_type"]);
				$empMedicalclaimsform->setDefault("other_disability_type",$data[0]["other_disability_type"]);
				$empMedicalclaimsform->setDefault("insurer_name",$data[0]["medical_insurer_name"]);
				$empMedicalclaimsform->setDefault("gp_name",$data[0]["concerned_physician_name"]);
				$empMedicalclaimsform->setDefault("hospital_name",$data[0]["hospital_name"]);
				$empMedicalclaimsform->setDefault("hospital_addr",$data[0]["hospital_address"]);
				$empMedicalclaimsform->setDefault("treatment_details",$data[0]["treatment_details"]);
				$empMedicalclaimsform->setDefault("room_num",$data[0]["room_number"]);
				$empMedicalclaimsform->setDefault("total_cost",$data[0]["total_cost"]);
				$empMedicalclaimsform->setDefault("amount_claimed",$data[0]["amount_claimed_for"]);
				$empMedicalclaimsform->setDefault("amount_approved",$data[0]["amount_approved"]);
				$empMedicalclaimsform->setDefault("leavebyemp_days",$data[0]["leavebyemployeer_days"]);
				$empMedicalclaimsform->setDefault("empleave_days",$data[0]["leaveappliedbyemployee_days"]);
					
				if($data[0]["injured_date"] != "" && $data[0]["injured_date"] != 0000-00-00)
				{
					$injureddate =sapp_Global::change_date($data[0]["injured_date"], 'view');
					$empMedicalclaimsform->setDefault('injured_date', $injureddate);
				}

				if($data[0]["expected_date_join"] != "" && $data[0]["expected_date_join"] != 0000-00-00)
				{
					$exp_dateofjoin =sapp_Global::change_date($data[0]["expected_date_join"], 'view');
					$empMedicalclaimsform->setDefault('expected_date_join', $exp_dateofjoin);
				}

				if($data[0]["leavebyemployeer_to_date"] != "" && $data[0]["leavebyemployeer_to_date"] != 0000-00-00)
				{
					$leavebyemployeer_to_date =sapp_Global::change_date($data[0]["leavebyemployeer_to_date"], 'view');
					$empMedicalclaimsform->setDefault("leavebyemp_to_date",$leavebyemployeer_to_date);
				}
				if($data[0]["leavebyemployeer_from_date"] != "" && $data[0]["leavebyemployeer_from_date"] != 0000-00-00)
				{
					$leavebyemployeer_from_date = sapp_Global::change_date($data[0]["leavebyemployeer_from_date"], 'view');
					$empMedicalclaimsform->setDefault("leavebyemp_from_date",$leavebyemployeer_from_date);
				}
				if($data[0]["leaveappliedbyemployee_to_date"] != "" && $data[0]["leaveappliedbyemployee_to_date"] != 0000-00-00)
				{
					$leaveappliedbyemployeetodate = sapp_Global::change_date($data[0]["leaveappliedbyemployee_to_date"], 'view');
					$empMedicalclaimsform->setDefault("empleave_to_date",$leaveappliedbyemployeetodate);
				}
				if($data[0]["leaveappliedbyemployee_from_date"] != "" && $data[0]["leaveappliedbyemployee_from_date"] != 0000-00-00)
				{
					$leaveappliedbyemployeefromdate = sapp_Global::change_date($data[0]["leaveappliedbyemployee_from_date"], 'view');
					$empMedicalclaimsform->setDefault("empleave_from_date",$leaveappliedbyemployeefromdate);
				}
				$this->view->data=$data;
			}
			$this->view->id=$id;
			$empMedicalclaimsform->setAttrib('action',BASE_URL.'medicalclaims/editpopup/unitId/'.$user_id);
		}
		$this->view->form = $empMedicalclaimsform;
		if($this->getRequest()->getPost())
		{	
			$errorResult = $this->medicalclaimsvalidations();
			if(empty($errorResult['msgarray']))
			{
				$result = $this->save($empMedicalclaimsform,$user_id);
				$this->view->msgarray = $result;
				$this->view->fieldValues = $errorResult['fieldValues'];
			}
			else
			{
				$this->view->msgarray = $errorResult['msgarray'];
				$this->view->fieldValues = $errorResult['fieldValues'];
			}
		}

	}

	public function deleteAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('medical_claims',$empOrganizationTabs)){
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->_request->getParam('objid');
		 	$messages['message'] = '';$messages['msgtype'] = '';
		 	$actionflag = 3;$messages['flagtype'] = '';
		 	if($id)
		 	{
		 		$empMedicalclaimsModel = new Default_Model_Medicalclaims();
		 		$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
		 		$where = array('id=?'=>$id);
		 		$Id = $empMedicalclaimsModel->SaveorUpdateEmpmedicalclaimsDetails($data, $where);
		 		if($Id == 'update')
		 		{
					$menuID = EMPLOYEE;
		 			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
		 			$messages['message'] = 'Employee medical claims  deleted successfully.';
		 			$messages['msgtype'] = 'success';
		 		}
		 		else
		 		{
		 			$messages['message'] = 'Employee medical claims cannot be deleted.';
		 			$messages['msgtype'] = 'error';		
		 		}
		 	}
		 	else
		 	{
		 		$messages['message'] = 'Employee medical claims cannot be deleted.';
		 		$messages['msgtype'] = 'error';
		 	}
		 	$this->_helper->json($messages);
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function save($empMedicalclaimsform,$userId)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		$errorflag = 'true';
		$msgarray = array();
		 
		 
		if($empMedicalclaimsform->isValid($this->_request->getPost()) && $errorflag == "true")
		{
			$empMedicalclaimsModel = new Default_Model_Medicalclaims();
			$id = $this->_request->getParam('id');
			$user_id = $this->getRequest()->getParam('user_id');
			//Post values ..
			$injuryIndicator = $this->_request->getParam('injuryindicator');
			$type = $this->_request->getParam('type');
			$injured_date =$this->_request->getParam('injured_date',null);
			$description =$this->_request->getParam('description');
			$injury_name=$this->_request->getParam('injury_name');
			$injury_severity=$this->_request->getParam('injury_severity');
			$disability_type=$this->_request->getParam('disability_type');
			$other_disability_type=$this->_request->getParam('other_disability_type');
			$insurer_name=$this->_request->getParam('insurer_name');
			$expected_date_join=$this->_request->getParam('expected_date_join',null);
			$leavebyemp_from_date=$this->_request->getParam('leavebyemp_from_date',null);
			$leavebyemp_to_date=$this->_request->getParam('leavebyemp_to_date',null);
			$leavebyemp_days=$this->_request->getParam('leavebyemp_days');
			$empleave_from_date=$this->_request->getParam('empleave_from_date',null);
			$empleave_to_date=$this->_request->getParam('empleave_to_date',null);
			$empleave_days=$this->_request->getParam('empleave_days');
			$hospital_name=$this->_request->getParam('hospital_name');
			$hospital_addr=$this->_request->getParam('hospital_addr');
			$room_num=$this->_request->getParam('room_num');
			$gp_name=$this->_request->getParam('gp_name');
			$treatment_details=$this->_request->getParam('treatment_details');
			$total_cost=$this->_request->getParam('total_cost');
			$amount_claimed=$this->_request->getParam('amount_claimed');
			$amount_approved=$this->_request->getParam('amount_approved');

			//Date Formats....
			$empleaveToDate="";$leavebyEmpFromDate="";$leavebyEmpToDate='';$empleaveFromDate='';


			$injuredDate = sapp_Global::change_date($injured_date, 'database');
			$expectedDateOfJoin = sapp_Global::change_date($expected_date_join, 'database');

			if($leavebyemp_from_date != "")
			{
				$leavebyEmpFromDate = sapp_Global::change_date($leavebyemp_from_date, 'database');
			}
			if($leavebyemp_to_date != "")
			{
				$leavebyEmpToDate =	sapp_Global::change_date($leavebyemp_to_date, 'database');
			}
			if($empleave_from_date != "")
			{
				$empleaveFromDate = sapp_Global::change_date($empleave_from_date, 'database');
			}
			if($empleave_to_date != "")
			{
				$empleaveToDate = sapp_Global::change_date($empleave_to_date, 'database');
			}

			$data = array(  'amount_approved'=>$amount_approved,
								'amount_claimed_for'=>$amount_claimed,
								'treatment_details'=>$treatment_details,
								'total_cost'=>$total_cost,
								'concerned_physician_name'=>$gp_name,
								'room_number'=>$room_num,
								'hospital_address'=>$hospital_addr,
								'hospital_name'=>$hospital_name,
								'leaveappliedbyemployee_days'=>$empleave_days,
								'leaveappliedbyemployee_to_date'=>$empleaveToDate,
								'leaveappliedbyemployee_from_date'=>$empleaveFromDate,
								'leavebyemployeer_days'=>$leavebyemp_days,
								'leavebyemployeer_to_date'=>$leavebyEmpToDate,
								'leavebyemployeer_from_date'=>$leavebyEmpFromDate,
								'expected_date_join'=>$expectedDateOfJoin,
								'medical_insurer_name'=>$insurer_name,
								'other_disability_type'=>$other_disability_type,
								'disability_type'=>$disability_type,
								'injury_severity'=>$injury_severity,
								'injury_name'=>$injury_name,
								'injury_description'=>$description,
								'injured_date'=>$injuredDate,
								'injury_type'=>$type,
								'injury_indicator'=>$injuryIndicator,
								'user_id'=>$user_id,
								'modifiedby'=>$loginUserId,
			                    'modifieddate'=>gmdate("Y-m-d H:i:s")
			);

			if($id!='')
			{
				$where = array('id=?'=>$id);
				$actionflag = 2;
			}
			else
			{
				$data['createdby'] = $loginUserId;
				$data['createddate'] = gmdate("Y-m-d H:i:s");
				$where = '';
				$actionflag = 1;
			}
			$Id = $empMedicalclaimsModel->SaveorUpdateEmpmedicalclaimsDetails($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee medical claims updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee medical claims added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);

			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");

			$close = 'close';
			$this->view->popup=$close;
			$this->view->controllername = 'medicalclaims';


		}
		else
		{
			$messages = $empMedicalclaimsform->getMessages();

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
	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

			if(in_array('medical_claims',$empOrganizationTabs)){
				$userID="";$conText = "";$searchArr=array('injury','disability','maternity','paternity');$objName="medicalclaims";
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
				}
				$userid = $this->getRequest()->getParam('userid');

				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
					$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
				}
				if($userid == '') $userid = $userID;
				$Uid = ($userid)?$userid:$userID;

				$empMedicalclaimsform = new Default_Form_Medicalclaims();
				$empMedicalclaimsModel = new Default_Model_Medicalclaims();
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
								$view = Zend_Layout::getMvcInstance()->getView();
								$objname = $this->_getParam('objname');
								$refresh = $this->_getParam('refresh');
								$dashboardcall = $this->_getParam('dashboardcall',null);
								$data = array();$searchQuery = '';	$searchArray = array();	$tablecontent = '';
								if($refresh == 'refresh')
								{
									if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
									else
									$perPage = PERPAGE;

									$sort = 'DESC';$by = 'm.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
								}
								else
								{
									$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
									$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'m.modifieddate';
									if($dashboardcall == 'Yes')
									$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
									else
									$perPage = $this->_getParam('per_page',PERPAGE);

									$pageNo = $this->_getParam('page', 1);
									$searchData = $this->_getParam('searchData');
									$searchData = rtrim($searchData,',');
								}
								$dataTmp = $empMedicalclaimsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

								array_push($data,$dataTmp);
								$this->view->id=$userid;
								$this->view->controllername = $objName;
								$this->view->dataArray = $data;
								$this->view->employeedata = $employeeData[0];
								$this->view->call = $call ;
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
		}
	}
	/*	Medical claims validations */
	public function medicalclaimsvalidations()
	{	$msgarray =array();$errFlag=0;	$fieldValues=array();$totalArr = array();
	if($this->getRequest()->getPost())
	{
		$type = $this->_request->getParam('type');
		$fieldValues['type']=($type != '')?$type:4;	//By default value is injury......
		 
		$injured_date =$this->_request->getParam('injured_date',null);
		$fieldValues['injured_date']=($injured_date != '')?date(DATEFORMAT_PHP, strtotime($injured_date)):'';
		 
		$description =$this->_request->getParam('description');
		$fieldValues['description']=($description != '')?$description:'';
		 
		$injury_name=$this->_request->getParam('injury_name');
		$fieldValues['injury_name']=($injury_name != '')?$injury_name:'';
		 
		$injury_severity=$this->_request->getParam('injury_severity');
		$fieldValues['injury_severity']=($injury_severity != '')?$injury_severity:'';
		 
		$disability_type=$this->_request->getParam('disability_type');
		$fieldValues['disability_type']=($disability_type != '')?$disability_type:'';
		 
		$other_disability_type=$this->_request->getParam('other_disability_type');
		$fieldValues['other_disability_type']=($other_disability_type != '')?$other_disability_type:'';
		 
		$insurer_name=$this->_request->getParam('insurer_name');
		$fieldValues['insurer_name']=($insurer_name != '')?$insurer_name:'';
		 
		$expected_date_join=$this->_request->getParam('expected_date_join',null);
		$fieldValues['expected_date_join']=($expected_date_join != '')?date(DATEFORMAT_PHP, strtotime($expected_date_join)):'';
		 
		$leavebyemp_from_date=$this->_request->getParam('leavebyemp_from_date',null);
		$fieldValues['leavebyemp_from_date']=($leavebyemp_from_date != '')?date(DATEFORMAT_PHP, strtotime($leavebyemp_from_date)):'';
		 
		$leavebyemp_to_date=$this->_request->getParam('leavebyemp_to_date',null);
		$fieldValues['leavebyemp_to_date']=($leavebyemp_to_date != '')?date(DATEFORMAT_PHP, strtotime($leavebyemp_to_date)):'';
		 
		$leavebyemp_days=$this->_request->getParam('leavebyemp_days');
		$fieldValues['leavebyemp_days']=($leavebyemp_days != '')?$leavebyemp_days:'';
		 
		$empleave_from_date=$this->_request->getParam('empleave_from_date',null);
		$fieldValues['empleave_from_date']=($empleave_from_date != '')?date(DATEFORMAT_PHP, strtotime($empleave_from_date)):'';
		 
		$empleave_to_date=$this->_request->getParam('empleave_to_date',null);
		$fieldValues['empleave_to_date']=($empleave_to_date != '')?date(DATEFORMAT_PHP, strtotime($empleave_to_date)):'';
		 
		$empleave_days=$this->_request->getParam('empleave_days');
		$fieldValues['empleave_days']=($empleave_days != '')?$empleave_days:'';
		 
		$hospital_name=$this->_request->getParam('hospital_name');
		$fieldValues['hospital_name']=($hospital_name != '')?$hospital_name:'';
		 
		$hospital_addr=$this->_request->getParam('hospital_addr');
		$fieldValues['hospital_addr']=($hospital_addr != '')?$hospital_addr:'';
		 
		$room_num=$this->_request->getParam('room_num');
		$fieldValues['room_num']=($room_num != '')?$room_num:'';
		 
		$gp_name=$this->_request->getParam('gp_name');
		$fieldValues['gp_name']=($gp_name != '')?$gp_name:'';
		 
		$treatment_details=$this->_request->getParam('treatment_details');
		$fieldValues['treatment_details']=($treatment_details != '')?$treatment_details:'';
		 
		$total_cost=$this->_request->getParam('total_cost');
		$fieldValues['total_cost']=($total_cost != '')?$total_cost:'';
		 
		$amount_claimed=$this->_request->getParam('amount_claimed');
		$fieldValues['amount_claimed']=($amount_claimed != '')?$amount_claimed:'';
		 
		$amount_approved=$this->_request->getParam('amount_approved');
		$fieldValues['amount_approved']=($amount_approved != '')?$amount_approved:'';
		 
		if($type != "")
		{
			$msgarray['type'] = $type;	$errFlag=0;
			switch($type)
			{
				case 1:	//Paternity
					if($injured_date == "")
					{
						//Error
						$msgarray['injured_date'] = 'Please select date.';
						$errFlag++;
					}
					if($insurer_name == "")
					{
						$msgarray['insurer_name'] = "Please enter insurer name.";
						$errFlag++;
					}
					if($expected_date_join == "")
					{
						$msgarray['expected_date_join'] = 'Please select date.';
						$errFlag++;
					}
					if($empleave_from_date =="")
					{
						$msgarray['empleave_from_date'] = 'Please select date.';
						$errFlag++;
					}
					if($empleave_to_date == "")
					{
						$msgarray['empleave_to_date'] = 'Please select date.';
						$errFlag++;
					}
					if($leavebyemp_from_date == "")
					{
						$msgarray['leavebyemp_from_date'] = 'Please select date.';
						$errFlag++;
					}
					if($leavebyemp_to_date == "")
					{
						$msgarray['leavebyemp_to_date'] = 'Please select date.';
					}

					if($hospital_name == "")
					{
						$msgarray['hospital_name'] = 'Please enter hospital name.';
						$errFlag++;
					}
					if($hospital_addr=="")
					{
						$msgarray['hospital_addr'] = 'Please enter hospital address.';
						$errFlag++;
					}
					if($room_num=="")
					{
						$msgarray['room_num'] = 'Please enter room/ward number.';
						$errFlag++;
					}
					if($gp_name=="")
					{
						$msgarray['gp_name'] = 'Please enter concerned physician name.';
						$errFlag++;
					}
					if($treatment_details == "")
					{
						$msgarray['treatment_details'] = 'Please enter treatment details.';
						$errFlag++;
					}
					if($total_cost == "")
					{
						$msgarray['total_cost'] = 'Please enter total cost.';
						$errFlag++;
					}
					if($amount_claimed== "")
					{
						$msgarray['amount_claimed'] = 'Please enter amount claimed for.';
						$errFlag++;
					}
					if($amount_approved== "")
					{
						$msgarray['amount_approved'] = 'Please enter amount approved.';
						$errFlag++;
					}
					if($amount_claimed !="" && $total_cost != "")
					{
						if($amount_claimed > $total_cost)
						{
							$msgarray['amount_claimed'] = 'Amount claimed should not be greater than total cost.';$errFlag++;
						}
					}
					if($amount_claimed !="" && $amount_approved != "")
					{
						if($amount_approved > $amount_claimed)
						{
							$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
						}
					}

					break;
				case 2:	//Maternity
					if($injured_date == "")
					{
						//Error
						$msgarray['injured_date'] = 'Please select date.';$errFlag++;
					}
					if($insurer_name == "")
					{
						$msgarray['insurer_name'] = "Please enter insurer name.";$errFlag++;
					}
					if($expected_date_join == "")
					{
						$msgarray['expected_date_join'] = 'Please select date.';$errFlag++;
					}
					if($empleave_from_date =="")
					{
						$msgarray['empleave_from_date'] = 'Please select date.';$errFlag++;
					}
					if($empleave_to_date == "")
					{
						$msgarray['empleave_to_date'] = 'Please select date.';$errFlag++;
					}
					if($leavebyemp_from_date == "")
					{
						$msgarray['leavebyemp_from_date'] = 'Please select date.';$errFlag++;
					}
					if($leavebyemp_to_date == "")
					{
						$msgarray['leavebyemp_to_date'] = 'Please select date.';$errFlag++;
					}

					if($hospital_name == "")
					{
						$msgarray['hospital_name'] = 'Please enter hospital name.';$errFlag++;
					}
					if($hospital_addr=="")
					{
						$msgarray['hospital_addr'] = 'Please enter hospital address.';$errFlag++;
					}
					if($room_num=="")
					{
						$msgarray['room_num'] = 'Please enter room/ward number.';$errFlag++;
					}
					if($gp_name=="")
					{
						$msgarray['gp_name'] = 'Please enter concerned physician name.';$errFlag++;
					}
					if($treatment_details == "")
					{
						$msgarray['treatment_details'] = 'Please enter treatment details.';$errFlag++;
					}
					if($total_cost == "")
					{
						$msgarray['total_cost'] = 'Please enter total cost.';		$errFlag++;
					}
					if($amount_claimed== "")
					{
						$msgarray['amount_claimed'] = 'Please enter amount claimed for.';	$errFlag++;
					}
					if($amount_approved== "")
					{
						$msgarray['amount_approved'] = 'Please enter amount approved.';$errFlag++;
					}
					if($amount_claimed !="" && $total_cost != "")
					{
						if($amount_claimed > $total_cost)
						{
							$msgarray['amount_claimed'] = 'Amount claimed should not be greater than total cost.';$errFlag++;
						}
					}
					if($amount_claimed !="" && $amount_approved != "")
					{
						if($amount_approved > $amount_claimed)
						{
							$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
						}
					}

					break;
				case 3:	//Disability
					if($injured_date == "")
					{
						//Error
						$msgarray['injured_date'] = 'Please select date.';$errFlag++;
					}
					if($injury_name == "")
					{
						//Error
						$msgarray['injury_name'] = 'Please enter disability.';$errFlag++;
					}
					if($insurer_name == "")
					{
						$msgarray['insurer_name'] = "Please enter insurer name.";$errFlag++;
					}
					if($expected_date_join == "")
					{
						$msgarray['expected_date_join'] = 'Please select date.';$errFlag++;
					}
					if($leavebyemp_from_date == "")
					{
						$msgarray['leavebyemp_from_date'] = 'Please select date.';$errFlag++;
					}
					if($leavebyemp_to_date == "")
					{
						$msgarray['leavebyemp_to_date'] = 'Please select date.';$errFlag++;
					}
					if($disability_type == "")
					{
						$msgarray['disability_type'] = 'Please select disability type.';$errFlag++;
					}
					if($disability_type!= "" && $disability_type == "other impairments" && $other_disability_type == "")
					{
						$msgarray['other_disability_type'] = 'Please enter any other disability type.';$errFlag++;
					}
					if($amount_claimed== "")
					{
						$msgarray['amount_claimed'] = 'Please enter amount claimed for.';	$errFlag++;
					}
					if($amount_approved== "")
					{
						$msgarray['amount_approved'] = 'Please enter amount approved.';$errFlag++;
					}
					if($amount_claimed !="" && $amount_approved != "")
					{
						if($amount_approved > $amount_claimed)
						{
							$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
						}
					}

					break;
				case 4:		//Injury
					if($injured_date == "")
					{
						//Error
						$msgarray['injured_date'] = 'Please select date.';$errFlag++;
					}
					if($injury_name == "")
					{
						//Error
						$msgarray['injury_name'] = 'Please enter injury.';$errFlag++;
					}
					if($injury_severity == "")
					{
						//Error
						$msgarray['injury_severity'] = 'Please select injury severity.';$errFlag++;
					}
					if($insurer_name == "")
					{
						$msgarray['insurer_name'] = "Please enter medical insurer name.";$errFlag++;
					}
					if($expected_date_join == "")
					{
						$msgarray['expected_date_join'] = 'Please select date.';$errFlag++;
					}
					if($empleave_from_date =="")
					{
						$msgarray['empleave_from_date'] = 'Please select date.';$errFlag++;
					}
					if($empleave_to_date == "")
					{
						$msgarray['empleave_to_date'] = 'Please select date.';$errFlag++;
					}
					if($leavebyemp_from_date == "")
					{
						$msgarray['leavebyemp_from_date'] = 'Please select date.';$errFlag++;
					}
					if($leavebyemp_to_date == "")
					{
						$msgarray['leavebyemp_to_date'] = 'Please select date.';$errFlag++;
					}

					if($hospital_name == "")
					{
						$msgarray['hospital_name'] = 'Please enter hospital name.';$errFlag++;
					}
					if($hospital_addr=="")
					{
						$msgarray['hospital_addr'] = 'Please enter hospital address.';$errFlag++;
					}
					if($room_num=="")
					{
						$msgarray['room_num'] = 'Please enter room/ward number.';$errFlag++;
					}
					if($gp_name=="")
					{
						$msgarray['gp_name'] = 'Please enter concerned physician name.';$errFlag++;
					}
					if($treatment_details == "")
					{
						$msgarray['treatment_details'] = 'Please enter treatment details.';$errFlag++;
					}
					if($total_cost == "")
					{
						$msgarray['total_cost'] = 'Please enter total cost.';	$errFlag++;
					}
					if($amount_claimed== "")
					{
						$msgarray['amount_claimed'] = 'Please enter amount claimed for.';	$errFlag++;
					}
					if($amount_approved== "")
					{
						$msgarray['amount_approved'] = 'Please enter amount approved.';$errFlag++;
					}
					if($amount_claimed !="" && $total_cost != "")
					{
						if($amount_claimed > $total_cost)
						{
							$msgarray['amount_claimed'] = 'Amount claimed should not be greater than total cost.';$errFlag++;
						}
					}
					if($amount_claimed !="" && $amount_approved != "")
					{
						if($amount_approved > $amount_claimed)
						{
							$msgarray['amount_approved'] = 'Amount approved should not be greater than amount claimed.';$errFlag++;
						}
					}
					break;
			}
		}
		if($errFlag == 0)	$msgarray ='';
		$totalArr['msgarray'] = $msgarray;
		$totalArr['fieldValues'] = $fieldValues;
		return $totalArr;
	}
	}

}