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

class Default_EmpholidaysController extends Zend_Controller_Action
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
		 if(in_array('emp_holidays',$empOrganizationTabs)){
		 	$emptyFlag=0;
		 	$conText = "";
		 	$userID="";
		 	$msgarray = array();
		 	$empGroupId = '';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginuserGroup = $auth->getStorage()->read()->group_id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$call = $this->_getParam('call');
		 	$holidaygroupid = $this->getRequest()->getParam('groupid');
		 	if($call == 'ajaxcall')
		 	{
		 		if($holidaygroupid !='')
		 		$_SESSION['holidaygroupid'] = $holidaygroupid;
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}else
		 	{
		 		unset($_SESSION['holidaygroupid']);
		 	}
		 	 
		 	 
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	if($Uid != "")
		 	{
		 		//TO dispaly EMployee Profile information.....
		 		$usersModel = new Default_Model_Users();
		 		$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
		 	}
		 	$employeesModel = new Default_Model_Employees();
		 	$holidaydatesmodel = new Default_Model_Holidaydates();
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
							if($id)
							{
								$empholidaysform = new Default_Form_empholidays();
								$holidaygroupModel = new Default_Model_Holidaygroups();
								$holidaygroupArr = $holidaygroupModel->getAllGroupData();

								if(!empty($holidaygroupArr))
								{
									foreach ($holidaygroupArr as $holidaygroupres){
										$empholidaysform->holiday_group->addMultiOption($holidaygroupres['id'],$holidaygroupres['groupname']);
									}
								}
								else
								{
									$msgarray['empholidaysform'] = 'Holiday groups not configured yet';
									$emptyFlag++;
								}
								$data = $employeesModel->getHolidayGroupForEmployee($id);

								if($data[0]['holiday_group'] !='')
								{
									$singleholidaygroupArr = $holidaygroupModel->getsingleGroupData($data[0]['holiday_group']);
									$empholidaysform->populate($data[0]);
									$empholidaysform->setDefault('holiday_group',$data[0]['holiday_group']);
									//$empGroupId = $data[0]['holiday_group'];
									$this->view->data = $data;
								}
								$empholidaysform->setAttrib('action',BASE_URL.'empholidays/edit/userid/'.$id);
								$this->view->form = $empholidaysform;
									
								if($holidaygroupid !='')
								{
									$empGroupId = $holidaygroupid;

								}
									
								else
								{
									if(isset($_SESSION['holidaygroupid']))
									{
										$empGroupId = $_SESSION['holidaygroupid'];
									}
									else if($data[0]['holiday_group'] !='')
									$empGroupId = $data[0]['holiday_group'];
									else
									$empGroupId = '';
								}
							}
							if($this->getRequest()->getPost())
							{
								$result = $this->save($empholidaysform,$id);
								$this->view->msgarray = $result;
							}

							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';$searchArray = array();$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;

								$sort = 'DESC';$by = 'h.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'h.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);

								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$objName = 'empholidays';

							$dataTmp = $holidaydatesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$objName,$empGroupId,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->id = $id ;
							$this->view->emptyFlag = $emptyFlag;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
							if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
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

		 if(in_array('emp_holidays',$empOrganizationTabs)){
		 	$emptyFlag=0;$conText = "";$userID="";$msgarray = array();	$empGroupId = '';
		 	$holidayGroupConfigPermission = '';
		 	$auth = Zend_Auth::getInstance();

		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginuserRole = $auth->getStorage()->read()->emprole;
		 		$loginuserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$holidayGroupConfigPermission = sapp_Global::_checkprivileges(HOLIDAYGROUPS,$loginuserGroup,$loginuserRole,'add');
		 	$this->view->holidayGroupConfigPermission = $holidayGroupConfigPermission;
		 	$id = $this->getRequest()->getParam('userid');
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}else
		 	{
		 		if(isset($_SESSION['holidaygroupid']))
		 		unset($_SESSION['holidaygroupid']);

		 	}
		 	if($id == '')		$id = $userID;
		 	$Uid = ($id)?$id:$userID;
		 	if($Uid != "")
		 	{
		 		//TO dispaly EMployee Profile information.....
		 		$usersModel = new Default_Model_Users();
		 		$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
		 	}
		 	$employeesModel = new Default_Model_Employees();
		 	$holidaydatesmodel = new Default_Model_Holidaydates();
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
		 				if($id)
		 				{
								$empholidaysform = new Default_Form_empholidays();
								$holidaygroupModel = new Default_Model_Holidaygroups();
								$holidaygroupArr = $holidaygroupModel->getAllGroupData();
								if(!empty($holidaygroupArr))
								{
									foreach ($holidaygroupArr as $holidaygroupres){
										$empholidaysform->holiday_group->addMultiOption($holidaygroupres['id'],$holidaygroupres['groupname']);
									}
								}
								else
								{

									$msgarray['holiday_group'] = 'Holiday groups are not configured yet.';

									$emptyFlag++;
								}

								$empholidaysform->holiday_group_name->setValue("Holiday group is not assigned yet.");
								$data = $employeesModel->getHolidayGroupForEmployee($id);

								if($data[0]['holiday_group'] !='')
								{
									$singleholidaygroupArr = $holidaygroupModel->getsingleGroupData($data[0]['holiday_group']);
									$empholidaysform->populate($data[0]);
									$empholidaysform->setDefault('holiday_group',$data[0]['holiday_group']);
									$empGroupId = $data[0]['holiday_group'];
									if(!empty($singleholidaygroupArr))
									$empholidaysform->holiday_group_name->setValue($singleholidaygroupArr['groupname']);
									$this->view->data = $data;
									$empholidaysform->setAttrib('action',BASE_URL.'empholidays/edit/userid/'.$id);
									$objname = $this->_getParam('objname');
									$refresh = $this->_getParam('refresh');
									$dashboardcall = $this->_getParam('dashboardcall',null);
									$data = array();	$searchQuery = '';$searchArray = array();$tablecontent = '';
									if($refresh == 'refresh')
									{
										if($dashboardcall == 'Yes')
										$perPage = DASHBOARD_PERPAGE;
										else
										$perPage = PERPAGE;

										$sort = 'DESC';$by = 'h.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();

									}
									else
									{
										$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
										$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'h.modifieddate';
										if($dashboardcall == 'Yes')
										$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
										else
										$perPage = $this->_getParam('per_page',PERPAGE);

										$pageNo = $this->_getParam('page', 1);
										$searchData = $this->_getParam('searchData');
										$searchData = rtrim($searchData,',');
									}
									$objName = 'empholidays';
									$dataTmp = $holidaydatesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$objName,$empGroupId,$Uid,$conText);

									array_push($data,$dataTmp);
									$this->view->dataArray = $data;
									$this->view->call = $call ;
								}
								$this->view->id = $id ;
								$this->view->emptyFlag = $emptyFlag;
								$this->view->messages = $this->_helper->flashMessenger->getMessages();
								if(!empty($employeeData))
								$this->view->employeedata = $employeeData[0];
								$this->view->form = $empholidaysform;
								$this->view->empdata = $empdata;
								$this->view->msgarray = $msgarray;
								if($this->getRequest()->getPost()){
									$result = $this->save($empholidaysform,$id);
									$this->view->msgarray = $result;
								}
		 				}
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

		 if(in_array('emp_holidays',$empOrganizationTabs)){
		 	$conText = "";$userID='';$msgarray = array();$empGroupId = '';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
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
		 	if($Uid != "")
		 	{
		 		//TO dispaly EMployee Profile information.....
		 		$usersModel = new Default_Model_Users();
		 		$employeeData = $usersModel->getUserDetailsByIDandFlag($Uid);
		 	}
		 	$employeesModel = new Default_Model_Employees();
		 	$holidaydatesmodel = new Default_Model_Holidaydates();
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
							if($id)
							{
								$empholidaysform = new Default_Form_empholidays();
								$holidaygroupModel = new Default_Model_Holidaygroups();
								$holidaygroupArr = $holidaygroupModel->getAllGroupData();
								if(sizeof($holidaygroupArr)>0)
								{
									$empGroupId = $holidaygroupArr[0]['id'];
									foreach ($holidaygroupArr as $holidaygroupres){
										$empholidaysform->holiday_group->addMultiOption($holidaygroupres['id'],$holidaygroupres['groupname']);
									}
								}
								else
								{
									$msgarray['empholidaysform'] = 'Holiday groups not configured yet';
								}
								$data = $employeesModel->getHolidayGroupForEmployee($id);
								if($data[0]['holiday_group'] !='')
								{
									$singleholidaygroupArr = $holidaygroupModel->getsingleGroupData($data[0]['holiday_group']);
									$empholidaysform->populate($data[0]);
									$empholidaysform->setDefault('holiday_group',$data[0]['holiday_group']);
									$empGroupId = $data[0]['holiday_group'];
									$this->view->data = $data;
								}
								$empholidaysform->setAttrib('action',BASE_URL.'empholidays/edit/userid/'.$id);
								$this->view->form = $empholidaysform;
							}
							if($this->getRequest()->getPost())
							{
								$result = $this->save($empholidaysform,$id);
								$this->view->msgarray = $result;
							}

							$objname = $this->_getParam('objname');
							$refresh = $this->_getParam('refresh');
							$dashboardcall = $this->_getParam('dashboardcall',null);
							$data = array();	$searchQuery = '';$searchArray = array();$tablecontent = '';
							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;

								$sort = 'DESC';$by = 'h.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();

							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'h.modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);

								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$objName = 'empholidays';
							$dataTmp = $holidaydatesmodel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$objName,$empGroupId,$Uid,$conText);
							array_push($data,$dataTmp);
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->id = $id ;
							$this->view->messages = $this->_helper->flashMessenger->getMessages();
							if(!empty($employeeData))
							$this->view->employeedata = $employeeData[0];
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

	public function save($empholidaysform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($empholidaysform->isValid($this->_request->getPost())){
			$employeesModel = new Default_Model_Employees();
			$holiday_group = $this->_request->getParam('holiday_group');
			$user_id = $userid;
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';
			if($user_id !='')
			{
				$data = array('holiday_group'=>$holiday_group,
				                'modifiedby'=>$loginUserId,
				                'modifieddate'=>gmdate("Y-m-d H:i:s")
				);
					
				$where = array('user_id=?'=>$user_id,
						               'isactive'=>1
				);
				$actionflag = 2;
				$Id = $employeesModel->SaveorUpdateEmployees($data, $where);
				if($Id == 'update')
				{
					$empdetailsArr = $employeesModel->getLoggedInEmployeeDetails($user_id);
					$tableid = $empdetailsArr[0]['id'];
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee holiday group updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee holiday group added successfully."));
				}
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			$this->_redirect('empholidays/edit/userid/'.$user_id);

		}else
		{
			$messages = $empholidaysform->getMessages();
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
		$callval = $this->getRequest()->getParam('call');
		if($callval == 'ajaxcall')
		$this->_helper->layout->disableLayout();
		$objName = 'empholidays';
		$holidaydatesform = new Default_Form_holidaydates();
		$holidaydatesmodel = new Default_Model_Holidaydates();
		$holidaydatesform->removeElement("submit");
		$data = $holidaydatesmodel->getsingleHolidayDatesData($id);
		$elements = $holidaydatesform->getElements();
		if(count($elements)>0)
		{
			foreach($elements as $key=>$element)
			{
				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
					$element->setAttrib("disabled", "disabled");
				}
			}
		}
			
		$holidaydatesform->populate($data);
			
		$holidaydate = sapp_Global::change_date($data['holidaydate'], 'view');
		$holidaydatesform->holidaydate->setValue($holidaydate);

		$this->view->controllername = $objName;
		$this->view->id = $id;
		$this->view->data = $data;
		$this->view->form = $holidaydatesform;
	}
}
