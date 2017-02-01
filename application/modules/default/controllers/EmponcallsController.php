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

class Default_EmponcallsController extends Zend_Controller_Action
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

		 if(in_array('emp_oncalls',$empOrganizationTabs)){
		 	$userID="";$conText=""; $currentdata = '';	   $oncalltransfercount = '';
		 	$previousyear = '';$isoncalltrasnferset = '';
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
		 	$employeeoncallsModel = new Default_Model_Employeeoncalls();
		 	$oncallmanagementModel = new Default_Model_Oncallmanagement();
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
					   $emponcallsform = new Default_Form_emponcalls();
					   $employeeoncallsModal = new Default_Model_Employeeoncalls();

					   $oncalltransferArr = $oncallmanagementModel->getWeekendDetails($empdata[0]['department_id']);
					   $prevyeardata = $employeeoncallsModal->getPreviousYearEmployeeoncallData($id);
					   $currentyeardata = $employeeoncallsModal->getsingleEmployeeoncallData($id);
					   if(empty($currentyeardata))
					   {
						$currentdata = "empty";
						$currentyearoncallcount ='';
					   }
					   else
					   {
						$currentdata = "notempty";
						$currentyearoncallcount = $currentyeardata[0]['emp_oncall_limit'];
					   }

					   $this->view->currentdata = $currentdata;

					   $data = $employeeoncallsModal->getsingleEmployeeoncallData($id);
					   $used_oncalls = 0;   $date=date('Y');
					  
					   if(!empty($data))
					   {
						$used_oncalls=$data[0]['used_oncalls'];
						$emponcallsform->oncall_limit->setValue($data[0]['emp_oncall_limit']);
					   }
					   $emponcallsform->alloted_year->setValue($date);

					   if(!empty($oncalltransferArr) && $oncalltransferArr[0]['is_oncalltransfer'] == 1 && !empty($prevyeardata) && is_numeric($prevyeardata[0]['remainingoncalls']) && (int)$prevyeardata[0]['remainingoncalls'] > 0 && $prevyeardata[0]['alloted_year'] !='' && empty($currentyeardata))
					   {
						$oncalltransfercount = $prevyeardata[0]['remainingoncalls'];
						$previousyear = $prevyeardata[0]['alloted_year'];
						$isoncalltrasnferset = 1;
						$emponcallsform->submitbutton->setAttrib('onClick','return showoncallalert('.$oncalltransfercount.','.$previousyear.')');
						$emponcallsform->setAttrib('action',BASE_URL.'emponcalls/edit/userid/'.$id);

					   }else
					   {
						$emponcallsform->setAttrib('action',BASE_URL.'emponcalls/edit/userid/'.$id);
					   }
					   $this->view->form = $emponcallsform;
					   $this->view->data = $data;
					   $this->view->id = $id;
					   $this->view->oncalltransfercount = $oncalltransfercount;

					}
					if($Uid != "")
					{
						$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
					}
					if($this->getRequest()->getPost())
					{
						$result = $this->save($emponcallsform,$id,$used_oncalls,$oncalltransfercount,$isoncalltrasnferset,$currentyearoncallcount);
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
					$dataTmp = $employeeoncallsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

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

		 if(in_array('emp_oncalls',$empOrganizationTabs)){
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
		 	$employeeoncallsModel = new Default_Model_Employeeoncalls();
		 	$oncallmanagementModel = new Default_Model_Oncallmanagement();
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
							$emponcallsform = new Default_Form_emponcalls();
							 $joiningdate =$empdata[0]['date_of_joining']; 
						   $empjoiningdate	=strtotime($joiningdate);
						   $empjoiningyear=date("Y",$empjoiningdate);
					
					     	$currentyear = date("Y");
			
						
						
							if(  $empjoiningyear <= $currentyear)
							{								
								$employeeoncallsModal = new Default_Model_Employeeoncalls();
								$currentdata = '';
								$oncalltransferArr = $oncallmanagementModel->getWeekendDetails($empdata[0]['department_id']);
								$prevyeardata = $employeeoncallsModal->getPreviousYearEmployeeoncallData($id);
								$currentyeardata = $employeeoncallsModal->getsingleEmployeeoncallData($id);
								if(empty($currentyeardata))
								{
									$currentdata = "empty";
									$currentyearoncallcount ='';
								}
								else
								{
									$currentdata = "notempty";
									$currentyearoncallcount = $currentyeardata[0]['emp_oncall_limit'];
								}
	
								$this->view->currentdata = $currentdata;
								$oncalltransfercount = '';
								$previousyear = '';
								$isoncalltrasnferset = '';
								$data = $employeeoncallsModal->getsingleEmployeeoncallData($id);
								$used_oncalls = 0;
								$date=date('Y');
							
								
								if(!empty($data))
								{
									$used_oncalls=$data[0]['used_oncalls'];
								}
								$emponcallsform->alloted_year->setValue($date);
	
								if(!empty($oncalltransferArr) && $oncalltransferArr[0]['is_oncalltransfer'] == 1 && !empty($prevyeardata) && is_numeric($prevyeardata[0]['remainingoncalls']) && (int)$prevyeardata[0]['remainingoncalls'] > 0 && $prevyeardata[0]['alloted_year'] !='' && empty($currentyeardata))
								{
									$oncalltransfercount = $prevyeardata[0]['remainingoncalls'];
									$previousyear = $prevyeardata[0]['alloted_year'];
									$isoncalltrasnferset = 1;
									$emponcallsform->submitbutton->setAttrib('onClick','return showoncallalert('.$oncalltransfercount.','.$previousyear.')');
									$emponcallsform->setAttrib('action',BASE_URL.'emponcalls/edit/userid/'.$id);
	
								}else
								{
									$emponcallsform->setAttrib('action',BASE_URL.'emponcalls/edit/userid/'.$id);
								}
								$this->view->form = $emponcallsform;
							
								$this->view->data = $data;
								$this->view->id = $id;
								$this->view->oncalltransfercount = $oncalltransfercount;
								$this->view->formflag = 'show';
							}
							else
							{
								$this->view->form = $emponcallsform; 
								$this->view->formflag = 'hide';
							}

						}
						if($this->getRequest()->getPost()){
							$result = $this->save($emponcallsform,$id,$used_oncalls,$oncalltransfercount,$isoncalltrasnferset,$currentyearoncallcount);
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
						$dataTmp = $employeeoncallsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
                    
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

			if(in_array('emp_oncalls',$empOrganizationTabs)){
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
				$employeeoncallsModel = new Default_Model_Employeeoncalls();
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
								$emponcallsform = new Default_Form_emponcalls();
								$employeeoncallsModal = new Default_Model_Employeeoncalls();
								$data = $employeeoncallsModal->getsingleEmployeeoncallData($id);
								$used_oncalls = 0;   $date=date('Y');
								if(!empty($data))
								{
									$emponcallsform->populate($data[0]);
									$used_oncalls=$data[0]['used_oncalls'];
								}
								$emponcallsform->alloted_year->setValue($date);
								$emponcallsform->setAttrib('action',BASE_URL.'emponcalls/edit/userid/'.$id);
								$this->view->form = $emponcallsform;
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
							$dataTmp = $employeeoncallsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

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

	public function save($emponcallsform,$userid,$used_oncalls,$oncalltransfercount,$isoncalltrasnferset,$currentyearoncallcount)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($emponcallsform->isValid($this->_request->getPost())){
			$employeeoncallsModel = new Default_Model_Employeeoncalls();
			$id = $this->_request->getParam('id');
			$user_id = $userid;
			$emp_oncall_limit = $this->_request->getParam('oncall_limit');
			if($oncalltransfercount !='' && $currentyearoncallcount =='')
			$emp_oncall_limit = ($emp_oncall_limit + $oncalltransfercount);
			else
			$emp_oncall_limit = ($emp_oncall_limit + $currentyearoncallcount);

			$isoncalltrasnfer = 0;
			if($isoncalltrasnferset == 1)
			$isoncalltrasnfer = 1;

			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
			/* Save employee oncalls in allotted oncalls log */
			$postedArr = array();
			$postedArr = $_POST;
			$saveID = $employeeoncallsModel->saveallotedoncalls($postedArr,$emp_oncall_limit,$user_id,$loginUserId);
			/* END */
			$Id = $employeeoncallsModel->SaveorUpdateEmployeeOncalls($user_id, $emp_oncall_limit,$isoncalltrasnfer,$loginUserId);
			if($id)
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Oncall details updated successfully."));
				$actionflag = 2;
				$tableid = $id;
			}
			else
			{
				$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee Oncall details added successfully."));
				$actionflag = 1;
				$tableid = $Id;
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->_redirect('emponcalls/edit/userid/'.$user_id);

		}else
		{
			$messages = $emponcallsform->getMessages();
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
		$objName = 'emponcalls';

		$emponcallsform = new Default_Form_emponcalls();
		$employeeoncallsModal = new Default_Model_Employeeoncalls();
		$oncallmanagementModel = new Default_Model_Oncallmanagement();
		$emponcallsform->removeElement("submit");
		$elements = $emponcallsform->getElements();
			
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
		$data = $employeeoncallsModal->getsingleEmponcallsrow($id);
		if(!empty($data))
			$oncallTypeCount = $oncallmanagementModel->getEmployeeUsedOncallsName($data['user_id'],$data['alloted_year']);
			
		if(!empty($data))
		{
			$emponcallsform->populate($data);
            $emponcallsform->oncall_limit->setValue($data['emp_oncall_limit']);
		}
			
		$this->view->controllername = $objName;
		$this->view->oncallTypeCount = $oncallTypeCount;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->form = $emponcallsform;
	}


}