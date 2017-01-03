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

class Default_VisaandimmigrationdetailsController extends Zend_Controller_Action
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
		
			if(in_array('visadetails',$empOrganizationTabs)){
				$userID="";$levelsArr=array();$empcompetencyLevelsArr =array();
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
								$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
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
		
									$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
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
								$dataTmp = $visaandimmigrationdetailsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
		
								array_push($data,$dataTmp);
								$this->view->dataArray = $data;
								$this->view->call = $call ;
								$this->view->employeedata = $employeeData[0];
								$this->view->id = $id;
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
	
			if(in_array('visadetails',$empOrganizationTabs)){
				$userID="";$levelsArr=array();
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
								$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
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
	
									$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
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
								$dataTmp = $visaandimmigrationdetailsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
	
								array_push($data,$dataTmp);
								$this->view->dataArray = $data;
								$this->view->call = $call ;
								$this->view->employeedata = $employeeData[0];
								$this->view->id = $id;
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
		 		
		 			if(in_array('visadetails',$empOrganizationTabs)){
		 				$userID="";$levelsArr=array();$empcompetencyLevelsArr =array();
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
		 								$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
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
		 		
		 									$sort = 'DESC';$by = 'e.modifieddate';$pageNo = 1;$searchData = '';$searchQuery = '';$searchArray='';
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
		 								$dataTmp = $visaandimmigrationdetailsModel->getGrid($sort, $by, $perPage, $pageNo,$searchData,$call,$dashboardcall,$Uid,$conText);
		 		
		 								array_push($data,$dataTmp);
		 								$this->view->dataArray = $data;
		 								$this->view->call = $call ;
		 								$this->view->employeedata = $employeeData[0];
		 								$this->view->id = $id;
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
		 	
		 	public function save($visaandimmigrationDetailsform,$userid)
		 	{
		 		$auth = Zend_Auth::getInstance();
		 		if($auth->hasIdentity()){
		 			$loginUserId = $auth->getStorage()->read()->id;
		 		}
		 		$passport_num = new Zend_Form_Element_Text('passport_number');
		 		$flag=true;
		 		$msgarray = array();
		 		$passportDetails=array();
		 		$passportNumber=$this->_request->getParam('passport_number');
		 		$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 		$passportDetails=$visaandimmigrationdetailsModel->getvisadetailsRecord($userid);
		 		/* if(!empty($passportDetails)) {
		 	    if($passportDetails[0]['passport_number']!=$passportNumber)
			 	    {
			 	    	
			 	    	$msgarray['passport_number'] = 'Enter Your correct passport number.';
			 	    	$flag=false;
			 	    }
		 		} */
		 		if(($visaandimmigrationDetailsform->isValid($this->_request->getPost()))&& $flag==true)
		 		{
		 			$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 			$id = $this->_request->getParam('id');
		 			$user_id = $userid;
		 			$passport_num = $this->_request->getParam('passport_number');
		 			$passport_issue_date = $this->_request->getParam('passport_issue_date',null);
		 			$passport_expiry_date = $this->_request->getParam('passport_expiry_date',null);
		 			$visa_number = $this->_request->getParam('visa_number');
		 			$visa_type = $this->_request->getParam('visa_type');
		 			$visa_issue_date = $this->_request->getParam('visa_issue_date',null);
		 			$visa_expiry_date = $this->_request->getParam('visa_expiry_date',null);
		 			$inine_status = $this->_request->getParam('inine_status');
		 			$inine_review_date = $this->_request->getParam('inine_review_date',null);
		 			$issuing_authority = $this->_request->getParam('issuing_authority');
		 			$ininetyfour_status = $this->_request->getParam('ininetyfour_status');
		 			$ininetyfour_expiry_date = $this->_request->getParam('ininetyfour_expiry_date',null);
		 	
		 			$passport_issue = sapp_Global::change_date($passport_issue_date, 'database');
		 			$passport_expiry = sapp_Global::change_date($passport_expiry_date, 'database');
		 			$visa_issue = sapp_Global::change_date($visa_issue_date, 'database');
		 			$visa_expiry = sapp_Global::change_date($visa_expiry_date, 'database');
		 			$inine_review = sapp_Global::change_date($inine_review_date, 'database');
		 			$ininetyfour_expiry = sapp_Global::change_date($ininetyfour_expiry_date, 'database');
		 	
		 			$data = array(  'passport_number'=>$passport_num,
		 					'passport_issue_date'=>$passport_issue,
		 					'passport_expiry_date'=>$passport_expiry,
		 					'visa_number'=>$visa_number,
		 					'visa_type'=>$visa_type,
		 					'visa_issue_date'=>$visa_issue,
		 					'visa_expiry_date'=>$visa_expiry,
		 					'inine_status'=>$inine_status,
		 					'inine_review_date'=>$inine_review,
		 					'issuing_authority'=>$issuing_authority,
		 					'ininetyfour_status'=>$ininetyfour_status,
		 					'ininetyfour_expiry_date'=>$ininetyfour_expiry,
		 					'user_id'=>$user_id,
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
		 				$where = '';
		 				$actionflag = 1;
		 			}
		 			$Id = $visaandimmigrationdetailsModel->SaveorUpdatevisaandimmigrationDetails($data, $where);
		 			if($Id == 'update')
		 			{
		 				$tableid = $id;
		 				$this->view->successmessage = 'Employee visa details updated successfully..';
		 			}
		 			else
		 			{
		 				$tableid = $Id;
		 				$this->view->successmessage = 'Employee visa details added successfully..';
		 			}
		 			$menuID = EMPLOYEE;
		 			$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
		 			$this->view->controllername = 'visaandimmigrationdetails';
		 			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		 	
		 	
		 		}
		 		else
		 		{
		 			$messages = $visaandimmigrationDetailsform->getMessages();
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
		 	
		 	public function addpopupAction()
		 	{
		 		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH."/layouts/scripts/popup/");
		 		$auth = Zend_Auth::getInstance();
		 		if($auth->hasIdentity())
		 		{
		 			$loginUserId = $auth->getStorage()->read()->id;
		 			$loginuserRole = $auth->getStorage()->read()->emprole;
		 			$loginuserGroup = $auth->getStorage()->read()->group_id;
		 		}
		 		$id = $this->getRequest()->getParam('unitId');
		 		if($id == '')
		 			$id = $loginUserId;
		 		// For open the form in popup...
		 		$emptyFlag=0;
		 		$Visaandimmigrationdetailsform = new Default_Form_Visaandimmigrationdetails();
		 		$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 		$msgarray = array();
		 		$Visaandimmigrationdetailsform->setAttrib('action',BASE_URL.'visaandimmigrationdetails/addpopup/unitId/'.$id);
		 		$this->view->form = $Visaandimmigrationdetailsform;
		 		$this->view->msgarray = $msgarray;
		 		$this->view->emptyFlag = $emptyFlag;
		 		$this->view->controllername = 'visaandimmigrationdetails';
		 		if($this->getRequest()->getPost())
		 		{
		 			$result = $this->save($Visaandimmigrationdetailsform,$id);
		 			$this->view->msgarray = $result;
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
		 	
		 		$Visaandimmigrationdetailsform = new Default_Form_Visaandimmigrationdetails();
		 		$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 		
		 		if($id)
		 		{
		 			$data = $visaandimmigrationdetailsModel->getsinglevisadetailsRecord($id);
		 			if(!empty($data))
		 			{
		 				$Visaandimmigrationdetailsform->populate($data[0]);
		 			}
		 		}
		 		$Visaandimmigrationdetailsform->setAttrib('action',BASE_URL.'visaandimmigrationdetails/editpopup/unitId/'.$userid);
		 		$this->view->form = $Visaandimmigrationdetailsform;
		 		$this->view->controllername = 'visaandimmigrationdetails';
		 	
		 		if($this->getRequest()->getPost())
		 		{
		 			$result = $this->save($Visaandimmigrationdetailsform,$userid);
		 			$this->view->msgarray = $result;
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
		 		$objName = 'visaandimmigrationdetails';
		 		$Visaandimmigrationdetailsform = new Default_Form_Visaandimmigrationdetails();
		 		$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 		$Visaandimmigrationdetailsform->removeElement("submit");
		 		$elements = $Visaandimmigrationdetailsform->getElements();
		 			
		 		if(count($elements)>0)
		 		{
		 			foreach($elements as $key=>$element)
		 			{
		 				if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
		 					$element->setAttrib("disabled", "disabled");
		 				}
		 			}
		 		}
		 			$data = $visaandimmigrationdetailsModel->getsinglevisadetailsRecord($id);
		 		if(!empty($data))
		 		{
		 			$Visaandimmigrationdetailsform->populate($data[0]);
		 	    }
		 		$this->view->controllername = $objName;
		 		$this->view->id = $id;
		 		$this->view->data = $data;
		 		$this->view->form = $Visaandimmigrationdetailsform;
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
		 			$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 			$data = array('isactive'=>0,'modifieddate'=>gmdate("Y-m-d H:i:s"));
		 			$where = array('id=?'=>$id);
		 			$Id = $visaandimmigrationdetailsModel->SaveorUpdatevisaandimmigrationDetails($data, $where);
		 			if($Id == 'update')
		 			{
		 				$menuID = EMPLOYEE;
		 				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$id);
		 				$messages['message'] = 'Employee visa details deleted successfully.';
		 				$messages['msgtype'] = 'success';
		 			}
		 			else
		 			{
		 				$messages['message'] = 'Employee visa details cannot be deleted.';
		 				$messages['msgtype'] = 'error';
		 			}
		 		}
		 		else
		 		{
		 			$messages['message'] = 'Employee visa details cannot be deleted.';
		 			$messages['msgtype'] = 'error';
		 		}
		 		$this->_helper->json($messages);
		 	
		 	}	 	
}

