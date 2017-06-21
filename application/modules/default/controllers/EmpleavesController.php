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

class Default_EmpleavesController extends Zend_Controller_Action
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

		 if(in_array('emp_leaves',$empOrganizationTabs)){
		 	$userID="";$conText=""; $currentdata = '';	   $leavetransfercount = '';
		 	$previousyear = '';$isleavetrasnferset = '';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginuserGroup = $auth->getStorage()->read()->group_id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 	}

		 	$id = $this->getRequest()->getParam('userid');
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	$employeeleavesModel = new Default_Model_Employeeleaves();
		 	$leavemanagementModel = new Default_Model_Leavemanagement();
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($id && is_numeric($id) && $id>0)
				{
					$isrowexist = $employeeModal->getsingleEmployeeData($id);
					if($isrowexist == 'norows')
				  $this->view->rowexist = "norows";
				  else
				  $this->view->rowexist = "rows";

				  $empdata = $employeeModal->getActiveEmployeeData($id);
				  if(!empty($empdata))
				  {
					$usersModel = new Default_Model_Users();
					$call = $this->_getParam('call');
					 
					if($id == '')
					$id = $loginUserId;
					if($id)
					{
					   $empleavesform = new Default_Form_empleaves();
					   $employeeleavesModal = new Default_Model_Employeeleaves();

					   $leavetransferArr = $leavemanagementModel->getWeekendDetails($empdata[0]['department_id']);
					   $prevyeardata = $employeeleavesModal->getPreviousYearEmployeeleaveData($id);
					   $currentyeardata = $employeeleavesModal->getsingleEmployeeleaveData($id);
					   if(empty($currentyeardata))
					   {
						$currentdata = "empty";
						$currentyearleavecount ='';
					   }
					   else
					   {
						$currentdata = "notempty";
						$currentyearleavecount = $currentyeardata[0]['emp_leave_limit'];
					   }

					   $this->view->currentdata = $currentdata;

					   $data = $employeeleavesModal->getsingleEmployeeleaveData($id);
					   $used_leaves = 0;   $date=date('Y');
					  
					   if(!empty($data))
					   {
						$used_leaves=$data[0]['used_leaves'];
						$empleavesform->leave_limit->setValue($data[0]['emp_leave_limit']);
					   }
					   $empleavesform->alloted_year->setValue($date);

					   if(!empty($leavetransferArr) && $leavetransferArr[0]['is_leavetransfer'] == 1 && !empty($prevyeardata) && is_numeric($prevyeardata[0]['remainingleaves']) && (int)$prevyeardata[0]['remainingleaves'] > 0 && $prevyeardata[0]['alloted_year'] !='' && empty($currentyeardata))
					   {
						$leavetransfercount = $prevyeardata[0]['remainingleaves'];
						$previousyear = $prevyeardata[0]['alloted_year'];
						$isleavetrasnferset = 1;
						$empleavesform->submitbutton->setAttrib('onClick','return showleavealert('.$leavetransfercount.','.$previousyear.')');
						$empleavesform->setAttrib('action',BASE_URL.'empleaves/edit/userid/'.$id);

					   }else
					   {
						$empleavesform->setAttrib('action',BASE_URL.'empleaves/edit/userid/'.$id);
					   }
					   $this->view->form = $empleavesform;
					   $this->view->data = $data;
					   $this->view->id = $id;
					   $this->view->leavetransfercount = $leavetransfercount;

					}
					if($Uid != "")
					{
						$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					}
					if($this->getRequest()->getPost())
					{
						$result = $this->save($empleavesform,$id,$used_leaves,$leavetransfercount,$isleavetrasnferset,$currentyearleavecount);
						$this->view->msgarray = $result;
					}
					$objname = $this->_getParam('objname');
					$refresh = $this->_getParam('refresh');
					$dashboardcall = $this->_getParam('dashboardcall',null);
					$data = array();$searchQuery = '';$searchArray = array();	$tablecontent = '';
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
					$dataTmp = $employeeleavesModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

					array_push($data,$dataTmp);
					$this->view->dataArray = $data;
					$this->view->call = $call ;
					$this->view->id = $id ;
					$this->view->messages = $this->_helper->flashMessenger->getMessages();
					if(!empty($employeeData))
					$this->view->employeedata = $employeeData[0];
					$this->view->usergroup = $loginuserGroup;
				  }
				  $this->view->empdata = $empdata;
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

		 if(in_array('emp_leaves',$empOrganizationTabs)){
		 	$userID="";
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginuserGroup = $auth->getStorage()->read()->group_id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 	}
		 	$conText="";
		 	$id = $this->getRequest()->getParam('userid');
		 	$call = $this->_getParam('call');
		 	$usersModel = new Default_Model_Users();
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	if($Uid != "")
		 	{
		 		$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
		 	}
		 	$employeeleavesModel = new Default_Model_Employeeleaves();
		 	$leavemanagementModel = new Default_Model_Leavemanagement();
		 	$employeeModal = new Default_Model_Employee();

		 	try
		 	{
			    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
				{
					$isrowexist = $employeeModal->getsingleEmployeeData($id);
					if($isrowexist == 'norows')
					$this->view->rowexist = "norows";
					else
					$this->view->rowexist = "rows";

					$empdata = $employeeModal->getActiveEmployeeData($id);
					//print_r($empdata); exit;
					//echo $empdata[0]['date_of_joining'];exit;
					//echo $currentdate=date("Y/m/d");exit;
					
					if(!empty($empdata))
					{

						$call = $this->_getParam('call');
							
						if($id == '')
						$id = $loginUserId;
						if($call == 'ajaxcall')
						{
							$this->_helper->layout->disableLayout();
							$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
						}
						if($id)
						{
							$empleavesform = new Default_Form_empleaves();
							 $joiningdate =$empdata[0]['date_of_joining']; 
						   $empjoiningdate	=strtotime($joiningdate);
						   $empjoiningyear=date("Y",$empjoiningdate);
					
					     	$currentyear = date("Y");
			
						
						
							if(  $empjoiningyear <= $currentyear)
							{								
								$employeeleavesModal = new Default_Model_Employeeleaves();
								$currentdata = '';
								$leavetransferArr = $leavemanagementModel->getWeekendDetails($empdata[0]['department_id']);
								$prevyeardata = $employeeleavesModal->getPreviousYearEmployeeleaveData($id);
								$currentyeardata = $employeeleavesModal->getsingleEmployeeleaveData($id);
								if(empty($currentyeardata))
								{
									$currentdata = "empty";
									$currentyearleavecount ='';
								}
								else
								{
									$currentdata = "notempty";
									$currentyearleavecount = $currentyeardata[0]['emp_leave_limit'];
								}
	
								$this->view->currentdata = $currentdata;
								$leavetransfercount = '';
								$previousyear = '';
								$isleavetrasnferset = '';
								$data = $employeeleavesModal->getsingleEmployeeleaveData($id);
								$used_leaves = 0;
								$date=date('Y');
							
								
								if(!empty($data))
								{
									$used_leaves=$data[0]['used_leaves'];
								}
								$empleavesform->alloted_year->setValue($date);
	
								if(!empty($leavetransferArr) && $leavetransferArr[0]['is_leavetransfer'] == 1 && !empty($prevyeardata) && is_numeric($prevyeardata[0]['remainingleaves']) && (int)$prevyeardata[0]['remainingleaves'] > 0 && $prevyeardata[0]['alloted_year'] !='' && empty($currentyeardata))
								{
									$leavetransfercount = $prevyeardata[0]['remainingleaves'];
									$previousyear = $prevyeardata[0]['alloted_year'];
									$isleavetrasnferset = 1;
									$empleavesform->submitbutton->setAttrib('onClick','return showleavealert('.$leavetransfercount.','.$previousyear.')');
									$empleavesform->setAttrib('action',BASE_URL.'empleaves/edit/userid/'.$id);
	
								}else
								{
									$empleavesform->setAttrib('action',BASE_URL.'empleaves/edit/userid/'.$id);
								}
								$this->view->form = $empleavesform;
							
								$this->view->data = $data;
								$this->view->id = $id;
								$this->view->leavetransfercount = $leavetransfercount;
								$this->view->formflag = 'show';
							}
							else
							{
								$this->view->form = $empleavesform; 
								$this->view->formflag = 'hide';
							}

						}
						if($this->getRequest()->getPost()){
							$result = $this->save($empleavesform,$id,$used_leaves,$leavetransfercount,$isleavetrasnferset,$currentyearleavecount);
							$this->view->msgarray = $result;
						}

						$objname = $this->_getParam('objname');
						$refresh = $this->_getParam('refresh');
						$dashboardcall = $this->_getParam('dashboardcall',null);
						$data = array();$searchQuery = '';$searchArray = array();	$tablecontent = '';
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
						$dataTmp = $employeeleavesModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
                    
						array_push($data,$dataTmp);
						$permission = sapp_Global::_checkprivileges(EMPLOYEE,$loginuserGroup,$loginUserRole,'edit');
						$this->view->dataArray = $data;
						$this->view->call = $call ;
						$this->view->id = $id ;
						$this->view->messages = $this->_helper->flashMessenger->getMessages();
						if(!empty($employeeData))
						$this->view->employeedata = $employeeData[0];
						$this->view->usergroup = $loginuserGroup;
						$this->view->permission = $permission;
					}
					$this->view->empdata = $empdata;
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

			if(in_array('emp_leaves',$empOrganizationTabs)){
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
				}
				$id = $this->getRequest()->getParam('userid');
				$conText="";
				$call = $this->_getParam('call');
				if($call == 'ajaxcall')
				{
					$this->_helper->layout->disableLayout();
					$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
					$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
				}
				$usersModel = new Default_Model_Users();
				if($id == '')	$id = $userID;
				$Uid = ($id)?$id:$userID;
				if($Uid != "")
				{

					$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
				}
				$employeeleavesModel = new Default_Model_Employeeleaves();
				$employeeModal = new Default_Model_Employee();
				try
				{
				    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
				    {
						$isrowexist = $employeeModal->getsingleEmployeeData($id);
						if($isrowexist == 'norows')
						$this->view->rowexist = "norows";
						else
						$this->view->rowexist = "rows";

						$empdata = $employeeModal->getActiveEmployeeData($id);
						if(!empty($empdata))
						{
							$usersModel = new Default_Model_Users();
							if($id)
							{
								$empleavesform = new Default_Form_empleaves();
								$employeeleavesModal = new Default_Model_Employeeleaves();
								$data = $employeeleavesModal->getsingleEmployeeleaveData($id);
								$used_leaves = 0;   $date=date('Y');
								if(!empty($data))
								{
									$empleavesform->populate($data[0]);
									$used_leaves=$data[0]['used_leaves'];
								}
								$empleavesform->alloted_year->setValue($date);
								$empleavesform->setAttrib('action',BASE_URL.'empleaves/edit/userid/'.$id);
								$this->view->form = $empleavesform;
								$this->view->data = $data;
								$this->view->id = $id;

							}
							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';	$searchArray = array();	$tablecontent = '';
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
							$dataTmp = $employeeleavesModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->id = $id ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
							if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
						}
						$this->view->empdata = $empdata;
					}else
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

	public function save($empleavesform,$userid,$used_leaves,$leavetransfercount,$isleavetrasnferset,$currentyearleavecount)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($empleavesform->isValid($this->_request->getPost())){
			$employeeleavesModel = new Default_Model_Employeeleaves();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$emp_leave_limit = $this->_request->getParam('leave_limit');
			if($leavetransfercount !='' && $currentyearleavecount =='')
			$emp_leave_limit = ($emp_leave_limit + $leavetransfercount);
			else
			$emp_leave_limit = ($emp_leave_limit + $currentyearleavecount);

			$isleavetrasnfer = 0;
			if($isleavetrasnferset == 1)
			$isleavetrasnfer = 1;

			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
			/* Save employee leaves in allotted leaves log */
			$postedArr = array();
			$postedArr = $_POST;
			$saveID = $employeeleavesModel->saveallotedleaves($postedArr,$emp_leave_limit,$user_id,$loginUserId);
			/* END */
			$Id = $employeeleavesModel->SaveorUpdateEmployeeLeaves($user_id, $emp_leave_limit,$isleavetrasnfer,$loginUserId);
			if($id)
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Leave details updated successfully."));
				$actionflag = 2;
				$tableid = $id;
			}
			else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Leave details added successfully."));
				$actionflag = 1;
				$tableid = $Id;
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->_redirect('empleaves/edit/userid/'.$user_id);

		}else
		{
			$messages = $empleavesform->getMessages();
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


	public function viewpopupAction()
	{
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->getRequest()->getParam('id');
		if($id == '')
		$id = $loginUserId;
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'empleaves';

		$empleavesform = new Default_Form_empleaves();
		$employeeleavesModal = new Default_Model_Employeeleaves();
		$leavemanagementModel = new Default_Model_Leavemanagement();
		$empleavesform->removeElement("submit");
		$elements = $empleavesform->getElements();
			
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$data = $employeeleavesModal->getsingleEmpleavesrow($id);
		if(!empty($data))
			$leaveTypeCount = $leavemanagementModel->getEmployeeUsedLeavesName($data['user_id'],$data['alloted_year']);
			
		if(!empty($data))
		{
			$empleavesform->populate($data);
            $empleavesform->leave_limit->setValue($data['emp_leave_limit']);
		}
			
		$this->view->controllername = $objName;
		$this->view->leaveTypeCount = $leaveTypeCount;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->form = $empleavesform;
	}


}