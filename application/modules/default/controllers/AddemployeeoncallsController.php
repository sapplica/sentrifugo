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

class Default_AddemployeeoncallsController extends Zend_Controller_Action
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
		$addemployeeoncallsModel = new Default_Model_Addemployeeoncalls();
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
		$dataTmp = $addemployeeoncallsModel->getGrid($sort, $by, $perPage, $pageNo, $searchData,$call,$dashboardcall,$loginUserId);

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
		$addEmpOncallsForm = new Default_Form_addemployeeoncalls();
		$bu_model = new Default_Model_Businessunits();
		$bu_arr = $bu_model->getBU_report();
		if(!empty($bu_arr))
        {
        	foreach ($bu_arr as $bu)
            {
            		$addEmpOncallsForm->businessunit_id->addMultiOption($bu['id'],$bu['bu_name']);
			}
        }
        else
        {
        	$msgarray['businessunit_id'] = 'Business Units are not added yet.';
        }
		$addEmpOncallsForm->alloted_year->setValue(date('Y'));
		$addEmpOncallsForm->setAttrib('action',BASE_URL.'addemployeeoncalls/add');
		$this->view->form = $addEmpOncallsForm; 
		$this->view->msgarray = $msgarray; 
		$this->view->ermsg = '';
		if($this->getRequest()->getPost()){
			 $result = $this->saveEmployeeOncalls($addEmpOncallsForm,$loginUserId);
			 $this->view->msgarray = $result; 
		}
	}
	
	public function saveEmployeeOncalls($addEmpOncallsForm,$loginUserId) {
		$addemployeeoncallsModel = new Default_Model_Addemployeeoncalls();
		$employeeoncallsModel = new Default_Model_Employeeoncalls();
		$oncallmanagementModel = new Default_Model_Oncallmanagement();
		$usermodel = new Default_Model_Employee();
		$businessunit_id = $this->_getParam('businessunit_id',null);
        $department_id = $this->_getParam('department_id',null);
        $user_ids = $this->_getParam('user_id',null);
		if($addEmpOncallsForm->isValid($this->_request->getPost()))
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
						$oncalltransfercount = 0;
						$isoncalltrasnferset = 0;
						$userId = $key;
						$emp_oncall_limit = $val;
						$empDetails = $usermodel->getEmp_from_summary($userId);
						$prevyeardata = $employeeoncallsModel->getPreviousYearEmployeeoncallData($userId);
						if(!empty($empDetails)) {
						$userDepartment = $empDetails['department_id'];
						$oncalltransferArr = $oncallmanagementModel->getWeekendDetails($userDepartment);
						}
							if(!empty($oncalltransferArr) && $oncalltransferArr[0]['is_oncalltransfer'] == 1) {
								$oncalltransfercount = !empty($prevyeardata)?$prevyeardata[0]['remainingoncalls']:0;
								$isoncalltrasnferset = 1;
							}
							$currentyeardata = $employeeoncallsModel->getsingleEmployeeoncallData($userId);
							if(empty($currentyeardata)) {
								$empOncallLimit = ($emp_oncall_limit + $oncalltransfercount);	
							}else{
								$empOncallLimit = ($emp_oncall_limit + $currentyeardata[0]['emp_oncall_limit']);
							} 
							/* Save employee oncalls in allotted oncalls log */
							$logID = $employeeoncallsModel->saveallotedoncalls($postedArr,$emp_oncall_limit,$userId,$loginUserId);
							
							$Id = $employeeoncallsModel->SaveorUpdateEmployeeOncalls($userId, $empOncallLimit,$isoncalltrasnferset,$loginUserId);
							$menuID = EMPLOYEE;
							$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$userId);
							$empOncallLimit = '';
					} 
				}
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Oncall details added successfully."));
					$this->_redirect('addemployeeoncalls');
					
			}
	        catch(Exception $e)
	      	{
	        	$msgarray['businessunit_id'] = "Something went wrong, please try again.";
	            return $msgarray;
	        }
		} else {
			$messages = $addEmpOncallsForm->getMessages();
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
                        $addEmpOncallsForm->department_id->addMultiOption($dept['id'],$dept['unitcode']." ".$dept['deptname']);
                    }
                }
			}
			if(!empty($department_id)) {
			 	$dept_id = implode(',', $department_id);
                $emp_data = $addemployeeoncallsModel->getMultipleEmployees($dept_id);
                if(!empty($emp_data))
                {
                    foreach($emp_data as $emp)
                    {
                        $addEmpOncallsForm->user_id->addMultiOption($emp['user_id']."-".$emp['department_id'],$emp['userfullname']);
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
				$addemployeeoncallsModel = new Default_Model_Addemployeeoncalls();
				$employeeoncallsModal = new Default_Model_Employeeoncalls();	
				$addEmpOncallsForm = new Default_Form_addemployeeoncalls();
				$empModel = new Default_Model_Employee();
				$currentYearData = $employeeoncallsModal->getsingleEmployeeoncallData($id); 
				
					$empDetails = $empModel->getEmp_from_summary($id);
					if(!empty($empDetails)) {
						$addEmpOncallsForm->businessunit_id->setValue($empDetails['businessunit_id']);
						$addEmpOncallsForm->department_id->setValue($empDetails['department_id']);
						$addEmpOncallsForm->user_id->setValue($empDetails['user_id']);
						if(!empty($currentYearData)) 
							$addEmpOncallsForm->oncall_limit->setValue($currentYearData[0]['emp_oncall_limit']);
						$addEmpOncallsForm->alloted_year->setValue(date('Y'));
						$addEmpOncallsForm->setAttrib('action',BASE_URL.'addemployeeoncalls/edit/id/'.$id);
						$this->view->form = $addEmpOncallsForm;
						$this->view->empdetails = $empDetails; 
						$this->view->msgarray = $msgarray; 
						$this->view->ermsg = '';
						if($this->getRequest()->getPost()){
							 $result = $this->update($addEmpOncallsForm,$currentYearData,$loginUserId,$empDetails['department_id']);
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
	
	public function update($addEmpOncallsForm,$currentYearData,$loginUserId,$userDepartment) {
		
		if($addEmpOncallsForm->isValid($this->_request->getPost()))
		{
			$userId = $this->getRequest()->getParam('user_id');
			$emp_oncall_limit = $this->_request->getParam('oncall_limit');
			$alloted_year = $this->_request->getParam('alloted_year');
			
			$addemployeeoncallsModel = new Default_Model_Addemployeeoncalls();
			$employeeoncallsModel = new Default_Model_Employeeoncalls();
			$oncallmanagementModel = new Default_Model_Oncallmanagement();
			$isoncalltrasnferset = 0;
			$oncalltransfercount = 0;
			if(!empty($userDepartment))
				$oncalltransferArr = $oncallmanagementModel->getWeekendDetails($userDepartment);
				
			$prevyeardata = $employeeoncallsModel->getPreviousYearEmployeeoncallData($userId);	
			if(!empty($oncalltransferArr) && $oncalltransferArr[0]['is_oncalltransfer'] == 1 && !empty($prevyeardata)) {
					$oncalltransfercount = $prevyeardata[0]['remainingoncalls'];
					$isoncalltrasnferset = 1;
			}
			if(empty($currentYearData)) {
				$emp_oncall_limit = ($emp_oncall_limit+$oncalltransfercount);
				$postedArr = array('oncall_limit'=>$emp_oncall_limit,'alloted_year'=>$alloted_year);
				$logID = $employeeoncallsModel->saveallotedoncalls($postedArr,$emp_oncall_limit,$userId,$loginUserId);
			}	
			else	
				$emp_oncall_limit = ($emp_oncall_limit+$currentYearData[0]['emp_oncall_limit']);
				
			$Id = $employeeoncallsModel->SaveorUpdateEmployeeOncalls($userId, $emp_oncall_limit,$isoncalltrasnferset,$loginUserId);
			$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Oncall details updated successfully."));
			$this->_redirect('addemployeeoncalls');
					
		}else {
			$messages = $addEmpOncallsForm->getMessages();
			foreach ($messages as $key => $val)
			{
				foreach($val as $key2 => $val2)
				 {
					$msgarray[$key] = $val2;
					break;
				 }
			}
			$addEmpOncallsForm->alloted_year->setValue(date('Y'));
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
		$objName = 'addemployeeoncalls';
		$id = $this->getRequest()->getParam('id');
		$msgarray = array();
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
			$this->_helper->layout->disableLayout();
		try 
		{	
			if($id && is_numeric($id) && $id>0)
			{		
				$addemployeeoncallsModel = new Default_Model_Addemployeeoncalls();	
				$addEmpOncallsForm = new Default_Form_addemployeeoncalls();
				$employeeoncallsModel = new Default_Model_Employeeoncalls();
				$empModel = new Default_Model_Employee();
				$currentYearData = $employeeoncallsModel->getsingleEmployeeoncallData($id); 
				$permission = sapp_Global::_checkprivileges(176,$loginuserGroup,$loginuserRole,'edit');
				$this->view->editpermission = $permission;
					$empDetails = $empModel->getEmp_from_summary($id);
					if(!empty($empDetails)) {
						$addEmpOncallsForm->businessunit_id->setValue($empDetails['businessunit_id']);
						$addEmpOncallsForm->department_id->setValue($empDetails['department_id']);
						$addEmpOncallsForm->user_id->setValue($empDetails['user_id']);
						$addEmpOncallsForm->oncall_limit->setAttrib("disabled", "disabled");
						if(!empty($currentYearData)) 
						$addEmpOncallsForm->oncall_limit->setValue($currentYearData[0]['emp_oncall_limit']);
						$addEmpOncallsForm->alloted_year->setValue(date('Y'));
						$this->view->form = $addEmpOncallsForm;
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