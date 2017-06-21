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

class Default_AddemployeeleavesController extends Zend_Controller_Action
{

	private $options;
	public function preDispatch()
	{
		//$ajaxContext = $this->_helper->getHelper('AjaxContext');
		//$ajaxContext->addActionContext('makeactiveinactive', 'json')->initContext();
	}
	
	public function indexAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
            $loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
		}
		$addemployeeleavesModel = new Default_Model_Addemployeeleaves();
		$call = $this->_getParam('call');
		if($call == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$view = Zend_Layout::getMvcInstance()->getView();
		$objname = $this->_getParam('objname');
		$refresh = $this->_getParam('refresh');
		$dashboardcall = $this->_getParam('dashboardcall',null);
		$data = array();$id='';
		$searchQuery = '';
		$searchArray = array();
		$tablecontent = '';
		if($refresh == 'refresh')
		{
			if($dashboardcall == 'Yes')
			$perPage = DASHBOARD_PERPAGE;
			else
			$perPage = PERPAGE;

			$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';
			$searchQuery = '';$searchArray='';
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
		$dataTmp = $addemployeeleavesModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$loginUserId);

		array_push($data,$dataTmp);
		$this->view->dataArray = $data;
		$this->view->call = $call;
		$this->view->messages = $this->_helper->flashMessenger->getMessages();
	}
	
	public function addAction() {
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserbusinessunit_id = $auth->getStorage()->read()->businessunit_id;
		}
		$msgarray = array();
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		$addEmpLeavesForm = new Default_Form_addemployeeleaves();
		$bu_model = new Default_Model_Businessunits();
		$bu_arr = $bu_model->getBU_report();
		if(!empty($bu_arr))
        {
        	foreach ($bu_arr as $bu)
            {
            		$addEmpLeavesForm->businessunit_id->addMultiOption($bu['id'],utf8_encode($bu['bu_name']));
			}
        }
        else
        {
        	$msgarray['businessunit_id'] = 'Business Units are not added yet.';
        }
		$addEmpLeavesForm->alloted_year->setValue(date('Y'));
		$addEmpLeavesForm->setAttrib('action',BASE_URL.'addemployeeleaves/add');
		$this->view->form = $addEmpLeavesForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
		if($this->getRequest()->getPost()){
			 $result = $this->saveEmployeeLeaves($addEmpLeavesForm,$loginUserId);
			 $this->view->msgarray = $result; 
		}
	}
	
	public function saveEmployeeLeaves($addEmpLeavesForm,$loginUserId) {
		$addemployeeleavesModel = new Default_Model_Addemployeeleaves();
		$employeeleavesModel = new Default_Model_Employeeleaves();
		$leavemanagementModel = new Default_Model_Leavemanagement();
		$usermodel = new Default_Model_Employee();
		$businessunit_id = $this->_getParam('businessunit_id',null);
        $department_id = $this->_getParam('department_id',null);
        $user_ids = $this->_getParam('user_id',null);
		if($addEmpLeavesForm->isValid($this->_request->getPost()))
		{
            try
            {
            	$postedArr = array();
	            $id = $this->_request->getParam('id');
				$postedArr = $this->_request->getPost();
				$actionflag = 1;
				$tableid  = '';
				if(!empty($user_ids)) {
					foreach($user_ids as $key => $val) {
						$leavetransfercount = 0;
						$isleavetrasnferset = 0;
						$userId = $key;
						$emp_leave_limit = $val;
						$empDetails = $usermodel->getEmp_from_summary($userId);
						$prevyeardata = $employeeleavesModel->getPreviousYearEmployeeleaveData($userId);
						if(!empty($empDetails)) {
						$userDepartment = $empDetails['department_id'];
						$leavetransferArr = $leavemanagementModel->getWeekendDetails($userDepartment);
						}
							if(!empty($leavetransferArr) && $leavetransferArr[0]['is_leavetransfer'] == 1) {
								$leavetransfercount = !empty($prevyeardata)?$prevyeardata[0]['remainingleaves']:0;
								$isleavetrasnferset = 1;
							}
							$currentyeardata = $employeeleavesModel->getsingleEmployeeleaveData($userId);
							if(empty($currentyeardata)) {
								$empLeaveLimit = ($emp_leave_limit + $leavetransfercount);	
							}else{
								$empLeaveLimit = ($emp_leave_limit + $currentyeardata[0]['emp_leave_limit']);
							} 
							/* Save employee leaves in allotted leaves log */
							$logID = $employeeleavesModel->saveallotedleaves($postedArr,$emp_leave_limit,$userId,$loginUserId);
							
							$Id = $employeeleavesModel->SaveorUpdateEmployeeLeaves($userId, $empLeaveLimit,$isleavetrasnferset,$loginUserId);
							$menuID = EMPLOYEE;
							$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$userId);
							$empLeaveLimit = '';
					} 
				}
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Leave details added successfully."));
					$this->_redirect('addemployeeleaves');
					
			}
	        catch(Exception $e)
	      	{
	        	$msgarray['businessunit_id'] = "Something went wrong, please try again.";
	            return $msgarray;
	        }
		} else {
			$messages = $addEmpLeavesForm->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				 {
					$msgarray[$key] = $val2;
					break;
				 }
			}
			if(!empty($businessunit_id)) {
			 	$bu_id = implode(',', $businessunit_id);
                $dept_model = new Default_Model_Departments();
                $dept_data = $dept_model->getDepartmentWithCodeList_bu($bu_id);
                if(!empty($dept_data))
                {
                    foreach($dept_data as $dept)
                    {
                        $addEmpLeavesForm->department_id->addMultiOption($dept['id'],$dept['unitcode']." ".$dept['deptname']);
                    }
                }
			}
			if(!empty($department_id)) {
			 	$dept_id = implode(',', $department_id);
                $emp_data = $addemployeeleavesModel->getMultipleEmployees($dept_id);
                if(!empty($emp_data))
                {
                    foreach($emp_data as $emp)
                    {
                        $addEmpLeavesForm->user_id->addMultiOption($emp['user_id']."-".$emp['department_id'],$emp['userfullname']);
                    }
                }
			}
			return $msgarray;	
		}
		
	}
	
	public function editAction() {
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserbusinessunit_id = $auth->getStorage()->read()->businessunit_id;
		}
		
		$id = $this->getRequest()->getParam('id');
		$msgarray = array();
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		try 
		{	
			if($id && is_numeric($id) && $id>0)
			{		
				$addemployeeleavesModel = new Default_Model_Addemployeeleaves();
				$employeeleavesModal = new Default_Model_Employeeleaves();	
				$addEmpLeavesForm = new Default_Form_addemployeeleaves();
				$empModel = new Default_Model_Employee();
				$currentYearData = $employeeleavesModal->getsingleEmployeeleaveData($id); 
				
					$empDetails = $empModel->getEmp_from_summary($id);
					if(!empty($empDetails)) {
						$addEmpLeavesForm->businessunit_id->setValue($empDetails['businessunit_id']);
						$addEmpLeavesForm->department_id->setValue($empDetails['department_id']);
						$addEmpLeavesForm->user_id->setValue($empDetails['user_id']);
						if(!empty($currentYearData)) 
							$addEmpLeavesForm->leave_limit->setValue($currentYearData[0]['emp_leave_limit']);
						$addEmpLeavesForm->alloted_year->setValue(date('Y'));
						$addEmpLeavesForm->setAttrib('action',BASE_URL.'addemployeeleaves/edit/id/'.$id);
						$this->view->form = $addEmpLeavesForm;
						$this->view->empdetails = $empDetails; 
						$this->view->msgarray = $msgarray; 
						$this->view->ermsg = '';
						if($this->getRequest()->getPost()){
							 $result = $this->update($addEmpLeavesForm,$currentYearData,$loginUserId,$empDetails['department_id']);
							 $this->view->msgarray = $result; 
						}
					}
					else
					{
					   $this->view->ermsg = "norows";
					}	
				
			}
			else
			{
			   $this->view->ermsg = "norows";
			}
		}
		catch(Exception $e)
	 	{
	 		$this->view->ermsg = "norows";
	 	}		
	}
	
	public function update($addEmpLeavesForm,$currentYearData,$loginUserId,$userDepartment) {
		
		if($addEmpLeavesForm->isValid($this->_request->getPost()))
		{
			$userId = $this->getRequest()->getParam('user_id');
			$emp_leave_limit = $this->_request->getParam('leave_limit');
			$alloted_year = $this->_request->getParam('alloted_year');
			
			$addemployeeleavesModel = new Default_Model_Addemployeeleaves();
			$employeeleavesModel = new Default_Model_Employeeleaves();
			$leavemanagementModel = new Default_Model_Leavemanagement();
			$isleavetrasnferset = 0;
			$leavetransfercount = 0;
			if(!empty($userDepartment))
				$leavetransferArr = $leavemanagementModel->getWeekendDetails($userDepartment);
				
			$prevyeardata = $employeeleavesModel->getPreviousYearEmployeeleaveData($userId);	
			if(!empty($leavetransferArr) && $leavetransferArr[0]['is_leavetransfer'] == 1 && !empty($prevyeardata)) {
					$leavetransfercount = $prevyeardata[0]['remainingleaves'];
					$isleavetrasnferset = 1;
			}
			if(empty($currentYearData)) {
				$emp_leave_limit = ($emp_leave_limit+$leavetransfercount);
				$postedArr = array('leave_limit'=>$emp_leave_limit,'alloted_year'=>$alloted_year);
				$logID = $employeeleavesModel->saveallotedleaves($postedArr,$emp_leave_limit,$userId,$loginUserId);
			}	
			else	
				$emp_leave_limit = ($emp_leave_limit+$currentYearData[0]['emp_leave_limit']);
				
			$Id = $employeeleavesModel->SaveorUpdateEmployeeLeaves($userId, $emp_leave_limit,$isleavetrasnferset,$loginUserId);
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Leave details updated successfully."));
			$this->_redirect('addemployeeleaves');
					
		}else {
			$messages = $addEmpLeavesForm->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				 {
					$msgarray[$key] = $val2;
					break;
				 }
			}
			$addEmpLeavesForm->alloted_year->setValue(date('Y'));
			return $msgarray;
		}	
		
	}
	
	public function viewAction() {
		$auth = Zend_Auth::getInstance();
     	if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginuserRole = $auth->getStorage()->read()->emprole;
			$loginuserGroup = $auth->getStorage()->read()->group_id;
			$loginuserbusinessunit_id = $auth->getStorage()->read()->businessunit_id;
		}
		$objName = 'addemployeeleaves';
		$id = $this->getRequest()->getParam('id');
		$msgarray = array();
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		try 
		{	
			if($id && is_numeric($id) && $id>0)
			{		
				$addemployeeleavesModel = new Default_Model_Addemployeeleaves();	
				$addEmpLeavesForm = new Default_Form_addemployeeleaves();
				$employeeleavesModel = new Default_Model_Employeeleaves();
				$empModel = new Default_Model_Employee();
				$currentYearData = $employeeleavesModel->getsingleEmployeeleaveData($id); 
				$permission = sapp_Global::_checkprivileges(176,$loginuserGroup,$loginuserRole,'edit');
				$this->view->editpermission = $permission;
					$empDetails = $empModel->getEmp_from_summary($id);
					if(!empty($empDetails)) {
						$addEmpLeavesForm->businessunit_id->setValue($empDetails['businessunit_id']);
						$addEmpLeavesForm->department_id->setValue($empDetails['department_id']);
						$addEmpLeavesForm->user_id->setValue($empDetails['user_id']);
						$addEmpLeavesForm->leave_limit->setAttrib("disabled", "disabled");
						if(!empty($currentYearData)) 
						$addEmpLeavesForm->leave_limit->setValue($currentYearData[0]['emp_leave_limit']);
						$addEmpLeavesForm->alloted_year->setValue(date('Y'));
						$this->view->form = $addEmpLeavesForm;
						$this->view->empdetails = $empDetails; 
						$this->view->msgarray = $msgarray; 
						$this->view->ermsg = '';
						$this->view->controllername = $objName;
						$this->view->id = $id;
						
					}
					else
					{
					   $this->view->ermsg = "norows";
					}	
			}
			else
			{
			   $this->view->ermsg = "norows";
			}
		}
		catch(Exception $e)
	 	{
	 		$this->view->ermsg = "norows";
	 	}		
	}
        
}//end of class