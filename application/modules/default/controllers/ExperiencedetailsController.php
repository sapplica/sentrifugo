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

class Default_ExperiencedetailsController extends Zend_Controller_Action
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

		 if(in_array('experience_details',$empOrganizationTabs)){
		 	$userID="";$objName = 'experiencedetails';
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity()){
					$loginUserId = $auth->getStorage()->read()->id;
					$loginUserRole = $auth->getStorage()->read()->emprole;
					$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	}

		 	$userid = $this->getRequest()->getParam('userid');//This is User_id taking from URL
		 	$conText = "";
		 	$call = $this->_getParam('call');
		 	if($call == 'ajaxcall')
		 	{
		 		$this->_helper->layout->disableLayout();
		 		$userID = ($this->_getParam('unitId') !='')? $this->_getParam('unitId'):$this->_getParam('userid');
		 		$conText = ($this->_getParam('context') !='')? $this->_getParam('context'):$this->getRequest()->getParam('context');
		 	}

		 	if($userid == '') $userid =$userID;
		 	$Uid = ($userid)?$userid:$userID;//die;

		 	//Check for this user id record exists or not....
		 	$experiencedetailsModel = new Default_Model_Experiencedetails();
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
							$data = array();$searchQuery = '';$searchArray = array();	$tablecontent = '';

							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;
								$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';	
								$searchQuery = '';$searchArray='';
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
							}

							$dataTmp = $experiencedetailsModel->getGrid($sort, $by, $pageNo, $perPage,$searchData,$call,$dashboardcall,$Uid,$conText);

							array_push($data,$dataTmp);
							$this->view->id=$userid;	
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
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

		 if(in_array('experience_details',$empOrganizationTabs)){
		 	$userID="";$conText = "";$objName = 'experiencedetails';
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

		 	//Check for this user id record exists or not....
		 	$experiencedetailsModel = new Default_Model_Experiencedetails();
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
							$data = array();$searchQuery = '';$searchArray = array();	$tablecontent = '';

							if($refresh == 'refresh')
							{
								if($dashboardcall == 'Yes')
								$perPage = DASHBOARD_PERPAGE;
								else
								$perPage = PERPAGE;
								$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';					
								$searchQuery = '';$searchArray='';
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
							$dataTmp = $experiencedetailsModel->getGrid($sort, $by, $pageNo, $perPage,$searchQuery,$call,$dashboardcall,$Uid,$conText);
							array_push($data,$dataTmp);
							$this->view->id=$userid;	
							$this->view->controllername = $objName;
							$this->view->dataArray = $data;
							$this->view->call = $call ;
							$this->view->employeedata = $employeeData[0];
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

			if(in_array('experience_details',$empOrganizationTabs)){
			    $auth = Zend_Auth::getInstance();
			    if($auth->hasIdentity()){
		 		$loginUserId = $auth->getStorage()->read()->id;
		 		$loginUserRole = $auth->getStorage()->read()->emprole;
		 		$loginUserGroup = $auth->getStorage()->read()->group_id;
		 	    }
				$objName = 'experiencedetails';$conText="";
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
				$experiencedetailsModel = new Default_Model_Experiencedetails();
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
								$data = array();$searchQuery = '';$searchArray = array();	$tablecontent = '';

								if($refresh == 'refresh')
								{
									if($dashboardcall == 'Yes')
									$perPage = DASHBOARD_PERPAGE;
									else
									$perPage = PERPAGE;

									$sort = 'DESC';$by = 'modifieddate';$pageNo = 1;$searchData = '';
									$searchQuery = '';$searchArray='';

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

								$dataTmp = $experiencedetailsModel->getGrid($sort, $by, $pageNo, $perPage,$searchData,$call,$dashboardcall,$Uid,$conText);

								array_push($data,$dataTmp);
								$this->view->id=$userid;	//User_id sending to view for tabs navigation....
								$this->view->controllername = $objName;
								$this->view->dataArray = $data;
								$this->view->call = $call ;
								$this->view->employeedata = $employeeData[0];
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
		// For open the form in popup...
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$experienceDetailsform = new Default_Form_Experiencedetails();

		$experienceDetailsform->setAttrib('action',BASE_URL.'experiencedetails/addpopup/unitId/'.$userId);
		$this->view->form = $experienceDetailsform;
		$this->view->msgarray = $msgarray;

		if($this->getRequest()->getPost())
		{
			$result = $this->validatereferrercontact($this->getRequest()->getPost());
			if(empty($result['msgarray']))
			{
				$result = $this->save($experienceDetailsform,$userId);
				$this->view->msgarray = $result;
			}else
			{
				$this->view->msgarray = $result['msgarray'];
				$this->view->fieldValues = $result['fieldValues'];
			}	
		}

	}
	public function validatereferrercontact($postParams)
	{
		$msgarray =array();$fieldValues=array();$totArr=array();
		if(!empty($postParams))
		{
			if($postParams['reference_contact'] != "" && $postParams['reference_contact'] == '0000000000')
			{
				$msgarray['reference_contact'] = 'Invalid referrer contact.';
			}
			$fieldValues['comp_name']=$postParams['comp_name'];
			$fieldValues['comp_website']=$postParams['comp_website'];
			$fieldValues['designation']=$postParams['designation'];
			$fieldValues['from_date']=($postParams['from_date'] != '')?date(DATEFORMAT_PHP, strtotime($postParams['from_date'])):'';
			$fieldValues['to_date']=($postParams['to_date'] != '')?date(DATEFORMAT_PHP, strtotime($postParams['to_date'])):'';
			$fieldValues['reason_for_leaving']=$postParams['reason_for_leaving'];
			$fieldValues['reference_name']=$postParams['reference_name'];
			$fieldValues['reference_contact']=$postParams['reference_contact'];
			$fieldValues['reference_email']=$postParams['reference_email'];

		}
		$totArr['msgarray']=$msgarray;
		$totArr['fieldValues']=$fieldValues;
		return $totArr;
	}
	
	public function editpopupAction()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		//For opening the form in pop up.....
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->_request->getParam('id');	//Taking Id(Primary key in table) from form....
		$user_id = $this->getRequest()->getParam('unitId');	//This is User_id taking from URL set to form...

		$experienceDetailsform = new Default_Form_Experiencedetails();
		$experienceDetailsModel = new Default_Model_Experiencedetails();
		if($id)
		{
			$data = $experienceDetailsModel->getexperiencedetailsRecord($id);
			$experienceDetailsform->setDefault("id",$data[0]["id"]);
			$experienceDetailsform->setDefault("user_id",$user_id);
			$experienceDetailsform->setDefault("comp_name",$data[0]["comp_name"]);
			$experienceDetailsform->setDefault("comp_website",$data[0]["comp_website"]);
			$experienceDetailsform->setDefault("designation",$data[0]["designation"]);
			$experienceDetailsform->setDefault("reference_name",$data[0]["reference_name"]);
			$experienceDetailsform->setDefault("reference_contact",$data[0]["reference_contact"]);
			$experienceDetailsform->setDefault("reference_email",$data[0]["reference_email"]);
			$fromdate = sapp_Global::change_date($data[0]["from_date"], 'view');
			$experienceDetailsform->setDefault('from_date', $fromdate);
			$todate = sapp_Global::change_date($data[0]["to_date"], 'view');
			$experienceDetailsform->setDefault('to_date', $todate);
			$experienceDetailsform->setDefault("reason_for_leaving",$data[0]["reason_for_leaving"]);
			$experienceDetailsform->setAttrib('action',BASE_URL.'experiencedetails/editpopup/unitId/'.$user_id);
			$this->view->id=$user_id;
		}
		$experienceDetailsform->setAttrib('action',BASE_URL.'experiencedetails/editpopup/id/'.$id.'/unitId/'.$user_id);
		$this->view->form = $experienceDetailsform;
		if($this->getRequest()->getPost())
		{
			$result = $this->validatereferrercontact($this->getRequest()->getPost());
			if(empty($result['msgarray']))
			{
				$result = $this->save($experienceDetailsform,$user_id);
				$this->view->msgarray = $result;
			}else
			{
				$this->view->msgarray = $result['msgarray'];
				$this->view->fieldValues = $result['fieldValues'];
			}	
		}
	}

	public function save($experienceDetailsform,$user_id)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		if($experienceDetailsform->isValid($this->_request->getPost()))
		{
			$experienceDetailsModel = new Default_Model_Experiencedetails();
			$id = $this->getRequest()->getParam('id');	
			$comp_name = $this->_request->getParam('comp_name');
			$comp_website = $this->_request->getParam('comp_website');
			$designation = $this->_request->getParam('designation');
			$reference_name = $this->_request->getParam('reference_name');
			$reference_contact = $this->_request->getParam('reference_contact');
			$reference_email = $this->_request->getParam('reference_email');
			$fromdate = $this->_request->getParam('from_date',null);
			$todate = $this->_request->getParam('to_date',null);
			$reason_for_leaving = $this->_request->getParam('reason_for_leaving');
			$data = array(  'comp_name'=>$comp_name,
								'comp_website'=>$comp_website,
								'designation'=>$designation,
								'from_date'=>sapp_Global::change_date($fromdate,'database'),
								'to_date'=>sapp_Global::change_date($todate,'database'),
								'reference_name'=>$reference_name,
								'reference_contact'=>$reference_contact,
								'reference_email'=>$reference_email,
								'reason_for_leaving'=>$reason_for_leaving,
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
			$Id = $experienceDetailsModel->SaveorUpdateEmployeeexperienceData($data,$where);
			if($Id == 'update')
			{
				$tableid = $id;
				$this->view->successmessage = 'Employee experience details updated successfully.';
			}
			else
			{
				$tableid = $Id;
				$this->view->successmessage = 'Employee experience details added successfully.';
			}
			$menuID = EMPLOYEE;
			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
			$close = 'close';
			$this->view->popup=$close;
			$this->view->controllername = 'experiencedetails';
		}
		else
		{
			$messages = $experienceDetailsform->getMessages();
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
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		//For opening the form in pop up.....
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		$id = $this->_request->getParam('id');	//Taking Id(Primary key in table) from form....
		$user_id = $this->getRequest()->getParam('unitId');	//This is User_id taking from URL set to form...
		$experienceDetailsform = new Default_Form_Experiencedetails();
		$experienceDetailsModel = new Default_Model_Experiencedetails();
		$experienceDetailsform->removeElement("submit");
		$elements = $experienceDetailsform->getElements();
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
			$data = $experienceDetailsModel->getexperiencedetailsRecord($id);
			$experienceDetailsform->setDefault("id",$data[0]["id"]);
			$experienceDetailsform->setDefault("user_id",$user_id);
			$experienceDetailsform->setDefault("comp_name",$data[0]["comp_name"]);
			$experienceDetailsform->setDefault("comp_website",$data[0]["comp_website"]);
			$experienceDetailsform->setDefault("designation",$data[0]["designation"]);
			$experienceDetailsform->setDefault("reference_name",$data[0]["reference_name"]);
			$experienceDetailsform->setDefault("reference_contact",$data[0]["reference_contact"]);
			$experienceDetailsform->setDefault("reference_email",$data[0]["reference_email"]);
			$fromdate = sapp_Global::change_date($data[0]["from_date"], 'view');
			$experienceDetailsform->setDefault('from_date', $fromdate);
			$todate = sapp_Global::change_date($data[0]["to_date"], 'view');
			$experienceDetailsform->setDefault('to_date', $todate);
			$experienceDetailsform->setDefault("reason_for_leaving",$data[0]["reason_for_leaving"]);
			$experienceDetailsform->setAttrib('action',BASE_URL.'experiencedetails/editpopup/unitId/'.$user_id);
			$this->view->id=$user_id;
		}
		$experienceDetailsform->setAttrib('action',BASE_URL.'experiencedetails/editpopup/id/'.$id.'/unitId/'.$user_id);
		$this->view->form = $experienceDetailsform;
		$this->view->data=$data;
		if($this->getRequest()->getPost())
		{
			$result = $this->save($experienceDetailsform,$user_id);
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
		$messages['message'] = '';$messages['msgtype'] = '';$messages['flagtype'] = '';
		$actionflag = 3;
		if($id)
		{
			$experienceDetailsModel = new Default_Model_Experiencedetails();
			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
			$where = array('id=?'=>$id);
			$Id = $experienceDetailsModel->SaveorUpdateEmployeeexperienceData($data, $where);
			if($Id == 'update')
			{
				$menuID = EMPLOYEE;
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
				$messages['message'] = 'Employee experience details deleted successfully.';
				$messages['msgtype'] = 'success';
			}
			else{
				$messages['message'] = 'Employee experience details  cannot be deleted.';
				$messages['msgtype'] = 'error';
			}
		}
		else
		{
			$messages['message'] = 'Employee experience details cannot be deleted.';
			$messages['msgtype'] = 'error';
		}
		$this->_helper->json($messages);
	}
}

