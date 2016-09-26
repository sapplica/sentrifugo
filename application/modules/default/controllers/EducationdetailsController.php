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

class Default_EducationdetailsController extends Zend_Controller_Action
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

		 if(in_array('education_details',$empOrganizationTabs)){

		 	$userID="";$conText = "";$objName = 'educationdetails';
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

		 	$educationdetailsModel = new Default_Model_Educationdetails();
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
							$data = array();$searchQuery = '';$searchArray = array();
							$tablecontent = '';
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
							$dataTmp = $educationdetailsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->id=$userid;
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->employeedata = $employeeData[0];
							$this->view->call = $call;
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

		 if(in_array('education_details',$empOrganizationTabs)){
		 	$userID="";$objName = 'educationdetails';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');
		 	$conText = "";
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid = $userID;
		 	$Uid = ($userid)?$userid:$userID;
		 	$educationdetailsModel = new Default_Model_Educationdetails();
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
							$data = array();$searchQuery = '';$searchArray = array();
							$tablecontent = '';
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
							$dataTmp = $educationdetailsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
							array_push($data,$dataTmp);
							$this->view->id=$userid;
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->employeedata = $employeeData[0];
							$this->view->call = $call;
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

		 if(in_array('education_details',$empOrganizationTabs)){
		    $auth = Zend_Auth::getInstance();
			if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
			$loginUserRole = $auth->getStorage()->read()->emprole;
			$loginUserGroup = $auth->getStorage()->read()->group_id;
			}
		 	$userID = '';
			$empdata=array();
			$conText = "";
			$objName = 'educationdetails';
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
		 	$educationdetailsModel = new Default_Model_Educationdetails();
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
							$data = array();$searchQuery = '';$searchArray = array();
							$tablecontent = '';
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
							$dataTmp = $educationdetailsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$userid,$conText);

							array_push($data,$dataTmp);
							$this->view->id=$userid;
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->employeedata = $employeeData[0];
							$this->view->call = $call;
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

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$userId = $this->getRequest()->getParam('unitId');

		// For open the form in popup...
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$educationDetailsform = new Default_Form_Educationdetails();
		$educationlevelcodemodel = new Default_Model_Educationlevelcode();
		$msgarray = array();
		$emptyFlag = '';
		$educationlevelArr = $educationlevelcodemodel->getEducationlevelData();
                $educationDetailsform->educationlevel->addMultiOption('','Select Education Level');
		if(!empty($educationlevelArr))
		{
			
			foreach ($educationlevelArr as $educationlevelres){
				$educationDetailsform->educationlevel->addMultiOption($educationlevelres['id'],$educationlevelres['educationlevelcode']);

			}
		}
		else
		{
			$msgarray['educationlevel'] = 'Educationl levels are not configured yet';
			$emptyFlag = 'no';
		}
			
		$educationDetailsform->setAttrib('action',BASE_URL.'educationdetails/addpopup/unitId/'.$userId);
		$this->view->form = $educationDetailsform;
		$this->view->msgarray = $msgarray;
		$this->view->emptyFlag = $emptyFlag;

		if($this->getRequest()->getPost())
		{
			$result = $this->save($educationDetailsform,$userId);
			$this->view->form = $educationDetailsform;
			$this->view->msgarray = $result;
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
		$educationDetailsform = new Default_Form_Educationdetails();
		$educationDetailsModel = new Default_Model_Educationdetails();
		$educationlevelcodemodel = new Default_Model_Educationlevelcode();
		$educationDetailsform->removeElement("submit");
		$elements = $educationDetailsform->getElements();
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
			$data = $educationDetailsModel->geteducationdetailsRecord($id);
			$singleeducationlevelArr = $educationlevelcodemodel->getsingleEducationLevelCodeData($data[0]['educationlevel']);
			if($singleeducationlevelArr !='norows')
			{
				$educationDetailsform->educationlevel->addMultiOption($singleeducationlevelArr[0]['id'],$singleeducationlevelArr[0]['educationlevelcode']);
             $data[0]['educationlevel']=$singleeducationlevelArr[0]['educationlevelcode'];
			}
			else
			{
				 $data[0]['educationlevel']="";
			}
			$educationDetailsform->setDefault("id",$id);
			$educationDetailsform->setDefault("institution_name",$data[0]["institution_name"]);
			$educationDetailsform->setDefault("course",$data[0]["course"]);
			$from_date = sapp_Global::change_date($data[0]['from_date'], 'view');
			$educationDetailsform->setDefault('from_date', $from_date);
			$to_date = sapp_Global::change_date($data[0]['to_date'], 'view');
			$educationDetailsform->setDefault('to_date', $to_date);
			$educationDetailsform->setDefault("percentage",$data[0]["percentage"]);
			$educationDetailsform->setAttrib('action',BASE_URL.'educationdetails/editpopup/userid/'.$id);
			$educationDetailsform->setDefault("user_id",$id);
			$this->view->id=$id;
		}
		$this->view->form = $educationDetailsform;
		$this->view->controllername = 'educationdetails';
		$this->view->data=$data;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($educationDetailsform,$id);
			$this->view->msgarray = $result;
		}
	}

	public function editpopupAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->getRequest()->getParam('id');	//Id (PK) from form URL
		$user_id = $this->getRequest()->getParam('unitId');	//This is User_id taking from URL set to form...
		$educationDetailsform = new Default_Form_Educationdetails();
		$educationDetailsModel = new Default_Model_Educationdetails();
		$educationlevelcodemodel = new Default_Model_Educationlevelcode();

		if($id)
		{
			$data = $educationDetailsModel->geteducationdetailsRecord($id);
			$educationlevelArr = $educationlevelcodemodel->getEducationlevelData();
			if(!empty($educationlevelArr))
			{
				$educationDetailsform->educationlevel->addMultiOption('','Select Education Level');
				foreach ($educationlevelArr as $educationlevelres){
					$educationDetailsform->educationlevel->addMultiOption($educationlevelres['id'],$educationlevelres['educationlevelcode']);

				}
			}

			$educationDetailsform->setDefault("id",$data[0]["id"]);
			$educationDetailsform->setDefault("educationlevel",$data[0]["educationlevel"]);
			$educationDetailsform->setDefault("institution_name",$data[0]["institution_name"]);
			$educationDetailsform->setDefault("course",$data[0]["course"]);
			$from_date = sapp_Global::change_date($data[0]['from_date'], 'view');
			$educationDetailsform->setDefault('from_date', $from_date);
			$to_date = sapp_Global::change_date($data[0]['to_date'], 'view');
			$educationDetailsform->setDefault('to_date', $to_date);
			$educationDetailsform->setDefault("percentage",$data[0]["percentage"]);
			$educationDetailsform->setAttrib('action',BASE_URL.'educationdetails/editpopup/id/'.$id.'/unitId/'.$user_id);
			$educationDetailsform->setDefault("user_id",$user_id);
			$this->view->id=$user_id;
		}
		$educationDetailsform->setAttrib('action',BASE_URL.'educationdetails/editpopup/id/'.$id.'/unitId/'.$user_id);
		$this->view->form = $educationDetailsform;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($educationDetailsform,$user_id);
			$this->view->msgarray = $result;
		}
	}

	public function save($educationDetailsform,$userid)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		if($educationDetailsform->isValid($this->_request->getPost())){
			$educationDetailsModel = new Default_Model_Educationdetails();
			$id = $this->getRequest()->getParam('id');
			$user_id = $userid;
			if($user_id == "")	$user_id = $loginUserId;
			$educationlevel = $this->_request->getParam('educationlevel');
			$institution_name = $this->_request->getParam('institution_name');
			$course = $this->_request->getParam('course');
			$from_date = $this->_request->getParam('from_date',null);
			$to_date = $this->_request->getParam('to_date',null);
			$percentage = $this->_request->getParam('percentage');
			$date = new Zend_Date();
			$actionflag = '';
			$tableid  = '';

			$data = array('user_id'=>$user_id,
				                 'educationlevel'=>$educationlevel,
				                 'institution_name'=>$institution_name,
								 'course'=>$course,
								 'from_date'=>sapp_Global::change_date($from_date, 'database'),					 
				      			 'to_date'=>sapp_Global::change_date($to_date, 'database'),
								 'percentage'=>$percentage,
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
			$Id = $educationDetailsModel->SaveorUpdateEducationDetails($data, $where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee education details updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee education details added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
			$close = 'close';
			$this->view->popup=$close;
			$this->view->controllername = 'educationdetails';
		}else
		{
			$messages = $educationDetailsform->getMessages();
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
		$messages['message'] = '';$messages['msgtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$educationDetailsModel = new Default_Model_Educationdetails();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $educationDetailsModel->SaveorUpdateEducationDetails($data, $where);
			if($Id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employee education details deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else
			{
				$messages['message'] = 'Employee education details  cannot be deleted.';
				$messages['msgtype'] = 'error';	
			}
		}
		else
		{
			$messages['message'] = 'Employee education details cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);
	}
}
