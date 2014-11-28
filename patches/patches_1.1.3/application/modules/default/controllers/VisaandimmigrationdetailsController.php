<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
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
	}

	public function addAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('visadetails',$empOrganizationTabs)){
		 	$auth = Zend_Auth::getInstance();$data=array();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$employeeModal = new Default_Model_Employee();
		 	$isrowexist = $employeeModal->getsingleEmployeeData($id);
		 	if($isrowexist == 'norows')
		  $this->view->rowexist = "norows";
		  else
		  $this->view->rowexist = "rows";

		  $empdata = $employeeModal->getActiveEmployeeData($id);
		  if(!empty($empdata))
		  {

		  	$visaandimmigrationDetailsform = new Default_Form_Visaandimmigrationdetails();
		  	$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		  	if($id)
		  	{
		  		$usersModel = new Default_Model_Users();
		  		$employeeData = $usersModel->getUserDetailsByIDandFlag($id);

		  		$this->view->id=$id;
		  		$visaandimmigrationDetailsform->setAttrib('action',DOMAIN.'visaandimmigrationdetails/add/userid/'.$id);
		  	}
		  	$this->view->form = $visaandimmigrationDetailsform;
		  	$this->view->employeedata = $employeeData[0];
		  	$this->view->data = $data;
		  	if($this->getRequest()->getPost())
		  	{
		  		$result = $this->save($visaandimmigrationDetailsform);
		  		$this->view->msgarray = $result;
		  	}
		  }
		  $this->view->empdata = $empdata;
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
		 	$auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$employeeModal = new Default_Model_Employee();
		 	$visaandimmigrationDetailsform = new Default_Form_Visaandimmigrationdetails();
		 	$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 	try
		 	{
			    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
				{
					$empdata = $employeeModal->getsingleEmployeeData($id);
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
							if($id)
							{
								$data = $visaandimmigrationdetailsModel->getvisadetailsRecord($id);
								if(!empty($data))
								{
									$visaandimmigrationDetailsform->setDefault("id",$data[0]["id"]);
									$visaandimmigrationDetailsform->setDefault("user_id",$data[0]["user_id"]);

									$visaandimmigrationDetailsform->setDefault("passport_number",$data[0]["passport_number"]);


									$pp_issue_date = sapp_Global::change_date($data[0]["passport_issue_date"], 'view');
									$visaandimmigrationDetailsform->setDefault('passport_issue_date', $pp_issue_date);


									$pp_expiry_date = sapp_Global::change_date($data[0]["passport_expiry_date"], 'view');
									$visaandimmigrationDetailsform->setDefault("passport_expiry_date",$pp_expiry_date);

									$visaandimmigrationDetailsform->setDefault("visa_number",$data[0]["visa_number"]);
									$visaandimmigrationDetailsform->setDefault("visa_type",$data[0]["visa_type"]);



									$v_issue_date = sapp_Global::change_date($data[0]["visa_issue_date"], 'view');
									$visaandimmigrationDetailsform->setDefault('visa_issue_date', $v_issue_date);


									$v_expiry_date = sapp_Global::change_date($data[0]["visa_expiry_date"], 'view');
									$visaandimmigrationDetailsform->setDefault("visa_expiry_date",$v_expiry_date);

									$visaandimmigrationDetailsform->setDefault("inine_status",$data[0]["inine_status"]);


									$inine_review = sapp_Global::change_date($data[0]["inine_review_date"], 'view');
									$visaandimmigrationDetailsform->setDefault("inine_review_date",$inine_review);

									$visaandimmigrationDetailsform->setDefault("issuing_authority",$data[0]["issuing_authority"]);
									$visaandimmigrationDetailsform->setDefault("ininetyfour_status",$data[0]["ininetyfour_status"]);


									$ininetyfour_expiry = sapp_Global::change_date($data[0]["ininetyfour_expiry_date"], 'view');
									$visaandimmigrationDetailsform->setDefault("ininetyfour_expiry_date",$ininetyfour_expiry);
								}
								$this->view->id=$id;
								$visaandimmigrationDetailsform->setAttrib('action',DOMAIN.'visaandimmigrationdetails/edit/userid/'.$id);
							}
							$this->view->form = $visaandimmigrationDetailsform;
							$this->view->employeedata = $empdata[0];
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
		 	if($this->getRequest()->getPost())
		 	{
		 		$result = $this->save($visaandimmigrationDetailsform);
		 		$this->view->msgarray = $result;
		 	}
		 }else{
		 	$this->_redirect('error');
		 }
		}else{
			$this->_redirect('error');
		}
	}

	public function save($visaandimmigrationDetailsform)
	{
		$result ="";
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$date = new Zend_Date();
		if($visaandimmigrationDetailsform->isValid($this->_request->getPost()))
		{
			$post_values = $this->_request->getPost();
           	if(isset($post_values['id']))
            	unset($post_values['id']);
            if(isset($post_values['user_id']))
                unset($post_values['user_id']);
            if(isset($post_values['submit']))	
                unset($post_values['submit']);
           	$new_post_values = array_filter($post_values);
           	$user_id = $this->_request->getParam('userid');
           	if(!empty($new_post_values))
           	{ 
				$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
				$id = $this->_request->getParam('id');
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
				if($id!='')
				{
					$where = array('user_id=?'=>$user_id);
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
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee visa details updated successfully."));
				}
				else
				{
					$tableid = $Id;
					$this->_helper->getHelper("FlashMessenger")->addMessage(array("success"=>"Employee visa details added successfully."));
				}
				$menumodel = new Default_Model_Menu();
				$menuidArr = $menumodel->getMenuObjID('/employee');
				$menuID = $menuidArr[0]['id'];
				$result = sapp_Global::logManager($menuID,$actionflag,$loginUserId,$user_id);
			 
			} else {
           		$this->_helper->getHelper("FlashMessenger")->addMessage(array("error"=>FIELDMSG));
			}			 
			$this->_redirect('visaandimmigrationdetails/edit/userid/'.$user_id);
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
	public function viewAction()
	{
		if(defined('EMPTABCONFIGS'))
		{
			$empOrganizationTabs = explode(",",EMPTABCONFIGS);

		 if(in_array('visadetails',$empOrganizationTabs)){
		    $auth = Zend_Auth::getInstance();
		 	if($auth->hasIdentity())
		 	{
		 		$loginUserId = $auth->getStorage()->read()->id;
		 	}
		 	$id = $this->getRequest()->getParam('userid');
		 	$objName = 'visaandimmigrationdetails';
		 	$callval = $this->getRequest()->getParam('call');
		 	if($callval == 'ajaxcall')
		 	$this->_helper->layout->disableLayout();
		 	$employeeModal = new Default_Model_Employee();
		 	$visaandimmigrationDetailsform = new Default_Form_Visaandimmigrationdetails();
		 	$visaandimmigrationdetailsModel = new Default_Model_Visaandimmigrationdetails();
		 	try
		 	{
			    if($id && is_numeric($id) && $id>0 && $id!=$loginUserId)
				{
					$empdata = $employeeModal->getsingleEmployeeData($id);
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
							$visaandimmigrationDetailsform->removeElement("submit");
							$elements = $visaandimmigrationDetailsform->getElements();
							if(count($elements)>0)
							{
								foreach($elements as $key=>$element)
								{
									if(($key!="Cancel")&&($key!="Edit")&&($key!="Delete")&&($key!="Attachments")){
										$element->setAttrib("disabled", "disabled");
									}
								}
							}
							$data = $visaandimmigrationdetailsModel->getvisadetailsRecord($id);
							if(!empty($data))
							{
								$visaandimmigrationDetailsform->setDefault("id",$data[0]["id"]);
								$visaandimmigrationDetailsform->setDefault("user_id",$data[0]["user_id"]);

								$visaandimmigrationDetailsform->setDefault("passport_number",$data[0]["passport_number"]);


								$pp_issue_date = sapp_Global::change_date($data[0]["passport_issue_date"], 'view');
								$visaandimmigrationDetailsform->setDefault('passport_issue_date', $pp_issue_date);


								$pp_expiry_date = sapp_Global::change_date($data[0]["passport_expiry_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("passport_expiry_date",$pp_expiry_date);

								$visaandimmigrationDetailsform->setDefault("visa_number",$data[0]["visa_number"]);
								$visaandimmigrationDetailsform->setDefault("visa_type",$data[0]["visa_type"]);



								$v_issue_date = sapp_Global::change_date($data[0]["visa_issue_date"], 'view');
								$visaandimmigrationDetailsform->setDefault('visa_issue_date', $v_issue_date);


								$v_expiry_date = sapp_Global::change_date($data[0]["visa_expiry_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("visa_expiry_date",$v_expiry_date);

								$visaandimmigrationDetailsform->setDefault("inine_status",$data[0]["inine_status"]);


								$inine_review = sapp_Global::change_date($data[0]["inine_review_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("inine_review_date",$inine_review);

								$visaandimmigrationDetailsform->setDefault("issuing_authority",$data[0]["issuing_authority"]);
								$visaandimmigrationDetailsform->setDefault("ininetyfour_status",$data[0]["ininetyfour_status"]);


								$ininetyfour_expiry = sapp_Global::change_date($data[0]["ininetyfour_expiry_date"], 'view');
								$visaandimmigrationDetailsform->setDefault("ininetyfour_expiry_date",$ininetyfour_expiry);
									
							}
							$this->view->controllername = $objName;
							$this->view->id = $id;
							$this->view->data = $data;
							$this->view->employeedata = $empdata[0];
							$this->view->form = $visaandimmigrationDetailsform;
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

}

