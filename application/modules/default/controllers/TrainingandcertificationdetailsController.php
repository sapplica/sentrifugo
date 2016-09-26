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

class Default_TrainingandcertificationdetailsController extends Zend_Controller_Action
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

		 if(in_array('trainingandcertification_details',$empOrganizationTabs)){
		 	$userID='';$empdata=array(); $conText = "";$objName = 'trainingandcertificationdetails';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');	//This is User_id taking from URL
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid =$userID;
		 	$Uid = ($userid)?$userid:$userID;
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($userid && is_numeric($userid) && $userid>0)
				{
					$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($userid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($userid);
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
							$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();
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

								$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $TandCdetailsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->id=$Uid;	
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

		 if(in_array('trainingandcertification_details',$empOrganizationTabs)){
		 	$userID='';$empdata=array(); $conText = "";$objName = 'trainingandcertificationdetails';
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
		 	if($userid == '') $userid =$userID;
		 	$Uid = ($userid)?$userid:$userID;
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($userid && is_numeric($userid) && $userid>0 && $userid!=$loginUserId)
				{
					$usersModel = new Default_Model_Users();
					$empdata = $employeeModal->getActiveEmployeeData($userid);
					$employeeData = $usersModel->getUserDetailsByIDandFlag($userid);
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
							$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();
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

								$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
							}
							else
							{
								$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
								$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
								if($dashboardcall == 'Yes')
								$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
								else
								$perPage = $this->_getParam('per_page',PERPAGE);
								$pageNo = $this->_getParam('page', 1);
								$searchData = $this->_getParam('searchData');
								$searchData = rtrim($searchData,',');
							}
							$dataTmp = $TandCdetailsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->id=$Uid;	
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

	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('trainingandcertification_details',$empOrganizationTabs)){
		 	$userID='';$empdata=array(); $conText = "";$objName = 'trainingandcertificationdetails';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}
		 	$userid = $this->getRequest()->getParam('userid');	//This is User_id taking from URL
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}
		 	if($userid == '') $userid =$userID;
		 	$Uid = ($userid)?$userid:$userID;
		 	$employeeModal = new Default_Model_Employee();
		 	try
		 	{
			    if($userid && is_numeric($userid) && $userid>0 && $userid!=$loginUserId)
				{
						$usersModel = new Default_Model_Users();
						$empdata = $employeeModal->getActiveEmployeeData($userid);
						$employeeData = $usersModel->getUserDetailsByIDandFlag($userid);
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
								$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();
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

									$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';	$searchArray = array();
								}
								else
								{
									$sort = ($this->_getParam('sort') !='')? $this->_getParam('sort'):'DESC';
									$by = ($this->_getParam('by')!='')? $this->_getParam('by'):'modifieddate';
									if($dashboardcall == 'Yes')
									$perPage = $this->_getParam('per_page',DASHBOARD_PERPAGE);
									else
									$perPage = $this->_getParam('per_page',PERPAGE);
									$pageNo = $this->_getParam('page', 1);
									$searchData = $this->_getParam('searchData');
									$searchData = rtrim($searchData,',');
								}
								$dataTmp = $TandCdetailsModel->getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);

								array_push($data,$dataTmp);
								$this->view->id=$Uid;	//User_id sending to view for tabs navigation....
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
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$userId = $this->getRequest()->getParam('unitId');

		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");

		$TandCDetailsform = new Default_Form_Trainingandcertificationdetails();

		$TandCDetailsform->setAttrib('action',BASE_URL.'trainingandcertificationdetails/addpopup/unitId/'.$userId);
		$this->view->form = $TandCDetailsform;
		$this->view->msgarray = $msgarray;

		if($this->getRequest()->getPost())
		{
			$result = $this->save($TandCDetailsform,$userId);
			$this->view->form = $TandCDetailsform;
			$this->view->msgarray = $result;
		}

	}
	public function editpopupAction()
	{
		$auth = Zend_Auth::getInstance();$issueddate ="";
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->_request->getParam('id');	
		$user_id = $this->getRequest()->getParam('unitId');	

		$TandCDetailsform = new Default_Form_Trainingandcertificationdetails();
		$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();

		if($id)
		{
			$data = $TandCdetailsModel->getTandCdetailsRecord($id);
			if(!empty($data))
			{
				$TandCDetailsform->setDefault("id",$data[0]["id"]);
				$TandCDetailsform->setDefault("user_id",$user_id);

				$TandCDetailsform->setDefault("course_name",$data[0]["course_name"]);
				$TandCDetailsform->setDefault("description",$data[0]["description"]);
				$TandCDetailsform->setDefault("course_level",$data[0]["course_level"]);

				$TandCDetailsform->setDefault("course_offered_by",$data[0]["course_offered_by"]);
				$TandCDetailsform->setDefault("certification_name",$data[0]["certification_name"]);
					
				if($data[0]["issued_date"] != "" && $data[0]["issued_date"] != "0000-00-00")
				{
					$issueddate = sapp_Global::change_date($data[0]["issued_date"], 'view');
					$TandCDetailsform->setDefault('issued_date', $issueddate);
				}
				$this->view->data=$data;
			}
			$TandCDetailsform->setAttrib('action',BASE_URL.'trainingandcertificationdetails/editpopup/unitId/'.$user_id);
			$this->view->id=$user_id;
		}
		$TandCDetailsform->setAttrib('action',BASE_URL.'trainingandcertificationdetails/editpopup/id/'.$id.'/unitId/'.$user_id);
		$this->view->form = $TandCDetailsform;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($TandCDetailsform,$user_id);
			$this->view->msgarray = $result;
		}
	}

	public function save($TandCDetailsform,$user_id)
	{
		$result ="";$issuedDateStr = "";$issuedDate ='';
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		if($TandCDetailsform->isValid($this->_request->getPost()))
		{
			$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();

			$id = $this->getRequest()->getParam('id');	

			$course_name = $this->_request->getParam('course_name');
			$description = $this->_request->getParam('description');
			$course_level = $this->_request->getParam('course_level');

			$course_offered_by = $this->_request->getParam('course_offered_by');
			$certification_name = $this->_request->getParam('certification_name');

			$issuedDate = $this->_request->getParam('issued_date',null);
			if($issuedDate != "")
			{
				$issuedDate = sapp_Global::change_date($issuedDate, 'database');
			}

			$data = array(  'course_name'=>$course_name,
								'description'=>$description,
								'course_level'=>$course_level,
								'course_offered_by'=>$course_offered_by,
								'certification_name'=>$certification_name,
								'issued_date'=>$issuedDate,
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
			$Id = $TandCdetailsModel->SaveorUpdateEmployeeTandCData($data,$where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee certification details updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee certification details added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");

			$close = 'close';
			$this->view->popup=$close;
			$this->view->controllername = 'trainingandcertificationdetails';
		}
		else
		{
			$messages = $TandCDetailsform->getMessages();

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
		$auth = Zend_Auth::getInstance();$issueddate = "";$issuedDateStr = "";
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->_request->getParam('id');	
		$user_id = $this->getRequest()->getParam('unitId');	

		$TandCDetailsform = new Default_Form_Trainingandcertificationdetails();
		$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();

		$TandCDetailsform->removeElement("submit");
		$elements = $TandCDetailsform->getElements();
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
			$data = $TandCdetailsModel->getTandCdetailsRecord($id);
			if(!empty($data))
			{
				$TandCDetailsform->setDefault("id",$data[0]["id"]);
				$TandCDetailsform->setDefault("user_id",$user_id);

				$TandCDetailsform->setDefault("course_name",$data[0]["course_name"]);
				$TandCDetailsform->setDefault("description",$data[0]["description"]);
				$TandCDetailsform->setDefault("course_level",$data[0]["course_level"]);

				$TandCDetailsform->setDefault("course_offered_by",$data[0]["course_offered_by"]);
				$TandCDetailsform->setDefault("certification_name",$data[0]["certification_name"]);

				if($data[0]["issued_date"] != "" && $data[0]["issued_date"] != "0000-00-00")
				{
					$issueddate = sapp_Global::change_date($data[0]["issued_date"], 'view');
					$TandCDetailsform->setDefault('issued_date', $issueddate);
				}
				$this->view->data=$data;
			}
			$TandCDetailsform->setAttrib('action',BASE_URL.'trainingandcertificationdetails/editpopup/unitId/'.$user_id);
			$this->view->id=$user_id;
		}
		$TandCDetailsform->setAttrib('action',BASE_URL.'trainingandcertificationdetails/editpopup/id/'.$id.'/unitId/'.$user_id);
		$this->view->form = $TandCDetailsform;
		$this->view->data=$data;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($TandCDetailsform,$user_id);
			$this->view->msgarray = $result;
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
			$TandCdetailsModel = new Default_Model_Trainingandcertificationdetails();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $TandCdetailsModel->SaveorUpdateEmployeeTandCData($data,$where);
			if($Id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employee certification details deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else{
				$messages['message'] = 'Employee certification details  cannot be deleted.';
				$messages['msgtype'] = 'error';	
			}
		}
		else
		{
			$messages['message'] = 'Employee certification details cannot be deleted.';
			$messages['msgtype'] = 'error';	
		}
		$this->_helper->json($messages);
	}

}

